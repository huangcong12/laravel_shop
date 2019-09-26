<?php

namespace App\Http\Controllers;

use App\Category;
use App\Exceptions\InternalException;
use App\OrderItem;
use App\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        // 如果有传入 category_id 字段，并且在数据库中有对应的类目
        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            // 如果这是一个父类目，查出该父类地下的所有子类
            if ($category->is_directory) {
                $builder->whereHas('category', function ($query) use ($category) {
                    $query->where('path', 'like', $category->path . $category->id . '-%');
                });
            } else {
                // 如果不是一个父类，直接筛选此类目下的商品
                $builder->where('category_id', $category->id);
            }
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
            ],
            'category' => $category ?? null,
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

        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku'])
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->limit(10)
            ->get();

        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews,
        ]);
    }

    /**
     * 搜藏商品展示页
     *
     * @param Request $request
     * @return Factory|View
     */
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
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
