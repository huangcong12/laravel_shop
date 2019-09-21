<?php

namespace App\Http\Controllers;

use App\Cartltem;
use App\Http\Requests\AddCartRequest;
use App\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * 显示购物车页面
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        $addresses = $request->user()->address()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', [
            'cartItems' => $cartItems,
            'addresses' => $addresses
        ]);
    }


    /**
     * 往购物车添加商品
     *
     * @param AddCartRequest $request
     * @return array
     */
    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->get('sku_id'), $request->get('amount'));

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
        $this->cartService->remove($cartItem->id);

        return [];
    }
}
