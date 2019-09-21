<?php

namespace App\Services;

use App\Cartltem;
use Auth;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();

        // 如果购物车中已存在该商品
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            $item->amount += $amount;
            $item->save();
        } else {
            $item = new Cartltem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return Auth::user()->cartItems()->whereIn('id', $ids)->delete();
    }

}
