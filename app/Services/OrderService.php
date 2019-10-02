<?php

namespace App\Services;

use App\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Order;
use App\ProductSku;
use App\User;
use App\UserAddress;
use Carbon\Carbon;
use DB;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
    {
        if ($coupon) {
            $coupon->checkAvailable($user);
        }

        return DB::transaction(function () use ($user, $address, $remark, $items, $coupon) {
            // 更新此地址的最后使用时间字段
            $address->update(['last_used_at' => Carbon::now()]);

            // 创建一个订单
            $order = new Order([
                'address' => [
                    'addresses' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $remark,
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;

            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);

                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price
                ]);

                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                // 减库存
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }

                if ($coupon) {
                    $coupon->checkAvailable($user, $totalAmount);
                    $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                    $order->couponCode()->associate($coupon);
                    if ($coupon->changeUsed() <= 0) {
                        throw new CouponCodeUnavailableException('该优惠券已被兑换完');
                    }
                }
            }

            // 更新金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中删除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            dispatch(new CloseOrder($order, config('app.order_ttl')));
            return $order;
        });
    }

    /**
     * 众筹下单
     *
     * @param User $user
     * @param UserAddress $address
     * @param ProductSku $sku
     * @param $amount
     */
    public function crowdfunding(User $user, UserAddress $address, ProductSku $sku, $amount)
    {
        $order = DB::transaction(function () use ($amount, $sku, $user, $address) {
            // 更新地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => '',
                'total_amount' => $sku->price * $amount
            ]);

            // 订单关联到当前用户
            $order->user()->associate($user);
            // 入库
            $order->save();

            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => $amount,
                'price' => $sku->price,
            ]);
            $item->product()->associate($sku->product_id);
            $item->productSku()->associate($sku);
            $item->save();

            // 扣减对应 SKU 库存
            if ($sku->decreaseStock($amount) <= 0) {
                throw  new InvalidRequestException('该商品库存不足');
            }
            return $order;
        });

        // 设置订单关闭时间
        $crowdfundingTtl = $sku->product->crowdfunding->end_at->getTimestamp() - time();
        // 剩余秒数与默认订单关闭时间取最小值作为订单关闭时间
        dispatch(new CloseOrder($order, min(config('app.order_ttl'), $crowdfundingTtl)));

        return $order;
    }

}
