<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * 首页
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $produccts = Product::query()->where('on_sale', true)->paginate(16);

        return view('products.index', ['products' => $produccts]);
    }
}
