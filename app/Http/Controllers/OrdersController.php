<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Order;
use App\ProductSku;
use App\UserAddress;
use Carbon\Carbon;
use DB;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        // 开启事物
        $order = DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));

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
                'remark' => $request->input('remark'),
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $items = $request->input('items');

            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);

                $item = $order->item()->make([
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

            return $order;
        });

        return $order;
    }
}
