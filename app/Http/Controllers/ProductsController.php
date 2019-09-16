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
}
