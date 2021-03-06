@extends('layouts.app')
@section('title', '评价')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    商品评价
                    <a href="{{ route('orders.index') }}">返回订单列表</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.review.store', [$order->id]) }}" method="post">
                        {{ csrf_field() }}
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>商品名称</td>
                                <td>打分</td>
                                <td>评价</td>
                            </tr>
                            @foreach($order->items as $index => $item)
                                <tr>
                                    <td class="product-info">
                                        <div class="preview">
                                            <a href="{{ route('products.show', [$item->product_id]) }}">
                                                <img src="{{ $item->product->image_url }}">
                                            </a>
                                        </div>
                                        <div>
                                            <span class="product-title">
                                                <a target="_blank"
                                                   href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                                            </span>
                                            <span class="sku-title">
                                                {{ $item->productSku->title }}
                                            </span>
                                        </div>
                                        <input type="hidden" name="reviews[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="vertical-middle">
                                        @if($order->reviewed)
                                            <span class="rating-start-yes">{{ str_repeat('★', $item->rating) }}</span>
                                            <span
                                                class="rating-start-no">{{ str_repeat('☆', 5 - $item->rating) }}</span>
                                        @else
                                            <ul class="rate-area">
                                                <input type="radio" id="5-start-{{ $index }}"
                                                       name="reviews[{{ $index }}][rating]" value="5" checked><label
                                                    for="5-start-{{ $index }}"></label>
                                                <input type="radio" id="4-start-{{ $index }}"
                                                       name="reviews[{{ $index }}][rating]" value="4"><label
                                                    for="4-start-{{ $index }}"></label>
                                                <input type="radio" id="3-start-{{ $index }}"
                                                       name="reviews[{{ $index }}][rating]" value="3"><label
                                                    for="3-start-{{ $index }}"></label>
                                                <input type="radio" id="2-start-{{ $index }}"
                                                       name="reviews[{{ $index }}][rating]" value="2"><label
                                                    for="2-start-{{ $index }}"></label>
                                                <input type="radio" id="1-start-{{ $index }}"
                                                       name="reviews[{{ $index }}][rating]" value="1"><label
                                                    for="1-start-{{ $index }}"></label>
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->reviewed)
                                            {{ $item->review }}
                                        @else
                                            <textarea
                                                class="form-control {{ $errors->has('reviews.'.$index.'.review') ? 'is-invalid' : '' }}"
                                                name="reviews[{{ $index }}][review]"></textarea>
                                            @if($errors->has('reviews.'.$index.'.review'))
                                                @foreach($errors->get('reviews.'.$index.'.review') as $msg)
                                                    <span class="invalid-feedback" role="alert"><stong>{{ $msg }}</stong></span>
                                                @endforeach
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-center">
                                    @if(!$order->reviewed)
                                        <button class="btn btn-primary center-block" type="submit">提交</button>
                                    @else
                                        <a href="{{ route('orders.show', [$order->id]) }}"
                                           class="btn btn-primary">查看订单</a>
                                    @endif
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
