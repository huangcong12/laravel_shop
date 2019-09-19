<?php

namespace App\Http\Controllers;

use App\Cartltem;
use App\Http\Requests\AddCartRequest;
use App\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * 显示购物车页面
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();

        return view('cart.index', ['cartItems' => $cartItems]);
    }


    /**
     * 往购物车添加商品
     *
     * @param AddCartRequest $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');

        // 如果购物车中已存在该商品
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            $cart->amount += $amount;
            $cart->save();
        } else {
            $cart = new Cartltem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }

    /**
     * 移除购物车里的物品
     *
     * @param ProductSku $productSku
     * @param Request $request
     * @return array
     */
    public function remove(Cartltem $cartItem, Request $request)
    {
        $request->user()->cartItems()->where('id', $cartItem->id)->delete();

        return [];
    }
}
