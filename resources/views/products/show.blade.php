@extends('layouts.app')
@section('title', $product->title)

@section('content')
    <div class="row" id="product-show">
        <div class="col-md-10 offset-lg-1">
            <div class="card">
                <div class="card-body product-info">
                    <div class="row">
                        <div class="col-5">
                            <img src="{{ $product->image_url }}" alt="" class="cover">
                        </div>
                        <div class="col-7">
                            <div class="title">{{ $product->title }}</div>
                            <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
                            <div class="sales_and_reviews">
                                <div class="sales_count">累计销量 <span class="count">{{ $product->sald_count }}</span>
                                </div>
                                <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span>
                                </div>
                                <div class="rating" title="评分 {{ $product->rating }}">评分 <span
                                        class="count">{{ str_repeat('★', floor($product->rating)) }}{{ floor($product->rating)==5?"":str_repeat('☆', 5-floor($product->rating)) }}</span>
                                </div>
                            </div>

                            <div class="skus">
                                <label>选择</label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    @foreach($product->skus as $sku)
                                        <label class="btn sku-btn" data-price="{{ $sku->price }}"
                                               data-stock="{{ $sku->stock }}"
                                               data-toggle="tooltip"
                                               title="{{ $sku->description }}" data-placement="bottom">
                                            <input type="radio" name="sku" autocomplete="off"
                                                   value="{{ $sku->id }}">{{ $sku->title }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="cart_amount">
                                <label>数量</label>
                                <input type="text" class="form-control form-control-sm" value="1">
                                <span>件</span>
                                <span class="stock"></span>
                            </div>

                            <div class="buttons">
                                @if($favored)
                                    <button class="btn btn-danger btn-disfavor">取消收藏</button>
                                @else
                                <button class="btn btn-success btn-favor">❤收藏</button>
                                @endif
                                <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                            </div>
                        </div>
                    </div>
                    <div class="product-detail">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="#product-detail-tab" class="nav-link active"
                                   aria-controls="product-detail-tab" role="tab" data-toggle="tab"
                                   aria-selected="true">商品详情</a>
                            </li>
                            <li class="nav-item">
                                <a href="#product-reviews-tab" class="nav-link" aria-controls="product-reviews-tab"
                                   role="tab" data-toggle="tab" aria-selected="false">用户评价</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" role="tabpanel" id="product-detail-tab">
                                {!! $product->description !!}
                            </div>
                            <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <td>用户</td>
                                        <td>商品</td>
                                        <td>评分</td>
                                        <td>评价</td>
                                        <td>时间</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>{{ $review->order->user->name }}</td>
                                            <td>{{ $review->productSku->title }}</td>
                                            <td>{{ str_repeat('★', $review->product->rating) }}{{ str_repeat('☆', 5-$review->product->rating) }}</td>
                                            <td>{{ $review->review }}</td>
                                            <td>{{ $review->reviewed_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
            $('.sku-btn').click(function () {
                $('.product-info .price span').text($(this).data('price'));
                $('.product-info .stock').text('库存：' + $(this).data('stock') + '件')
            });
            $('.sku-btn:first').trigger('click');
        });

        // 增加收藏
        $('.btn-favor').on('click', function () {
            axios.post('{{ route('products.favor', ['product'=> $product->id]) }}')
                .then(function () {
                    swal('操作成功', '', 'success')
                }, function (error) {
                    if (error.response && error.response.status === 401) {
                        swal('请先登录', '', 'error').then(function () {
                            location.href = '{{ route('login') }}'
                        });
                    } else if (error.response && (error.response.data.msg || error.response.data.message)) {
                        swal(error.response.data.msg ? error.response.data.msg : error.response.data.message, '', 'error')
                    } else {
                        swal('系统异常', '', 'error')
                    }
                });
        });

        // 取消收藏
        $('.btn-disfavor').on('click', function () {
            axios.delete('{{ route("products.disfavor", ['product' => $product->id]) }}')
                .then(function () {
                    swal('操作成功', '', 'success').then(function () {
                        location.reload()
                    });
                });
        });

        // 添加到购物车
        $('.btn-add-to-cart').on('click', function () {
            axios.post('{{ route('cart.add') }}', {
                sku_id: $('label.active input[name=sku]').val(),
                amount: $('.cart_amount input').val()
            }).then(function () {
                swal('加入购物车成功', '', 'success').then(function () {
                    location.href = '{{ route('cart.index') }}'
                })
            }, function (error) {
                if (error.response && error.response.status === 401) {
                    swal('请先登录', '', 'error').then(function () {
                        location.href = '{{ route('login') }}'
                    });
                } else if (error.response && (error.response.data.msg || error.response.data.message)) {
                    swal(error.response.data.msg ? error.response.data.msg : error.response.data.message, '', 'error')
                } else {
                    swal('系统异常', '', 'error')
                }
            });
        });
    </script>
@endsection
