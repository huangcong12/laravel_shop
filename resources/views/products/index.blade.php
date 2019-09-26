@extends('layouts.app')
@section('title', '商品列表')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    {{-- 筛选组件--}}
                    <form action="{{ route('products.index') }}" class="search-form">
                        <div class="form-row">
                            <div class="col-md-9">
                                <div class="form-row">
                                    {{-- 面包屑 --}}
                                    <div class="col-auto category-breadcrumb">
                                        <a class="all-products" href="{{ route('products.index') }}">全部</a>
                                        @if($category)
                                            @foreach($category->ancestors as $ancestors)
                                                <span class="category">
                                                    <a href="{{ route('products.index', ['category_id' => $ancestors->id]) }}">{{ $ancestors->name }}</a>
                                                </span>
                                                <span>&gt;</span>
                                            @endforeach
                                            <span class="category">{{ $category->name }}</span>
                                            <input type="hidden" name="category_id" value="{{ $category->id }}">
                                        @endif
                                    </div>
                                    <div class="col-auto"><input type="text" class="form-control form-control-sm"
                                                                 placeholder="搜索" name="search"></div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-primary">搜索</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="order" class="form-control form-control-sm" id="">
                                    <option value="">排序方式</option>
                                    <option value="price_desc">价格从高到低</option>
                                    <option value="price_asc">价格从低到高</option>
                                    <option value="sold_count_desc">销量从高到低</option>
                                    <option value="sold_count_asc">销量从低到高</option>
                                    <option value="rating_desc">评价从高到低</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    {{-- 展示子类目 --}}
                    <div class="filters">
                        @if($category && $category->is_directory)
                            <div class="row">
                                <div class="col-3 filter-key">子类目：</div>
                                <div class="col-9 filter-value">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('products.index', ['category_id' => $child->id]) }}">{{ $child->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row products-list">
                        @foreach($products as $product)
                            <div class="col-3 product-item">
                                <div class="product-content">
                                    <div class="top">
                                        <a href="{{ route('products.show', ['product'=>$product->id]) }}">
                                            <div class="img"><img src="{{ $product->image_url }}" alt=""></div>
                                        </a>
                                        <div class="price"><b>￥</b>{{ $product->price }}</div>
                                        <div class="title">{{ $product->title }}</div>
                                    </div>

                                    <div class="bottom">
                                        <div class="sold-count">销量<span>{{ $product->sold_count }}笔</span></div>
                                        <div class="review_count">评价<span>{{ $product->rating }}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                    <div class="float-right">{{ $products->appends($filters)->render() }}</div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scriptsAfterJs')
    <script>
        var filters = {!! json_encode($filters) !!}
        $(document).ready(function () {
            $('.search-form [name=search]').val(filters.search);
            $('.search-form [name=order]').val(filters.order);

            $('.search-form [name=order]').on('change', function () {
                $('.search-form').submit()
            })
        })
    </script>
@endsection
