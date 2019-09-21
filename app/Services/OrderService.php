<?php

namespace App\Services;

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
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        return DB::transaction(function () use ($user, $address, $remark, $items) {
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
            }

            // 更新金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中删除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            dispatch(new CloseOrder($order))->delay(now()->addSecond(config('app.order_ttl')));
            return $order;
        });
    }

}
