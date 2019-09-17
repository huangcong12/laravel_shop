<?php

namespace App\Http\Controllers;

use App\Exceptions\InternalException;
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
        $builder = Product::query()->where('on_sale', true);
        if ($search = $request->get('search', '')) {
            $like = '%' . $search . '%';
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        $order = $request->get('order', '');
        if ($order && preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
            if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                $builder->orderBy($m[1], $m[2]);
            }
        }

        $produccts = $builder->paginate(16);

        return view('products.index', [
            'products' => $produccts,
            'filters' => [
                'search' => $search,
                'order' => $order
            ]
        ]);
    }

    /**
     * 商品详情页
     *
     * @param Product $product
     * @param Request $request
     */
    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InternalException('商品未上架');
        }

        $favored = false;
        if ($request->user()) {
            $favored = boolval($request->user()->favoriteProducts()->find($product->id));
        }

        return view('products.show', ['product' => $product, 'favored' => $favored]);
    }

    /**
     * 收藏商品
     *
     * @param Product $product
     * @param Request $request
     */
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    /**
     * 取消收藏
     *
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function disFavor(Product $product, Request $request)
    {
        $request->user()->favoriteProducts()->detach($product);
        return [];
    }
}
