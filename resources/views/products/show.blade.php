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
                            @if($product->type === \App\Product::TYPE_CROWDFUNDING)
                                {{-- 众筹商品 --}}
                                <div class="crowdfunding-info">
                                    <div class="have-text">已筹到</div>
                                    <div class="total-amount">
                                        <span class="symbol">￥</span>
                                        {{ $product->crowdfunding->total_amount }}
                                    </div>
                                    {{-- 这里使用 bootstrap 的进度条 --}}
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success progress-bar-striped"
                                             role="progressbar"
                                             aria-valuenow={{ $product->crowdfunding->percent }}
                                             aria-valuemin="0" aria-valuemax="100"
                                             style="min-width:1em;width:{{ min($product->crowdfunding->percent, 100) }}%">
                                        </div>
                                    </div>
                                    <div class="progress-info">
                                        <span
                                            class="current-progress">当前进度：{{ $product->crowdfunding->percent }}%</span>
                                        <span
                                            class="float-right user-count">{{ $product->crowdfunding->user_count }}名支持者</span>
                                    </div>
                                    {{-- 如果状态是众筹中，输出提示语--}}
                                    @if($product->crowdfunding->status === \App\CrowdfundingProduct::STATUS_FUNDING)
                                        <div>此项目必须在
                                            <span
                                                class="text-red">{{ $product->crowdfunding->end_at->format('Y-m-d H:i:s') }}</span>
                                            前得到
                                            <span class="text-red">￥{{ $product->crowdfunding->target_amount }}</span>
                                            的支持才可成功，筹款将在
                                            <span
                                                class="text-red">{{ $product->crowdfunding->end_at->diffForHumans(now()) }}</span>
                                            结束！
                                        </div>
                                    @endif
                                </div>
                            @else
                                {{-- 普通商品 --}}
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
                            @endif

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

                                {{-- 众筹商品 --}}
                                @if($product->type == \App\Product::TYPE_CROWDFUNDING)
                                    @if(Auth::check())
                                        @if($product->crowdfunding->status === \App\CrowdfundingProduct::STATUS_FUNDING)
                                            <button class="btn btn-primary btn-crowdfunding">参与众筹</button>
                                        @else
                                            <button class="btn btn-primary disabled">
                                                {{ \App\CrowdfundingProduct::$statusMap[$product->crowdfunding->status] }}
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-primary">请先登录</a>
                                    @endif
                                @else
                                        <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                                @endif
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
                    swal('收藏成功', '', 'success').then(function () {
                        location.reload()
                    });
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
                    swal('取消收藏成功', '', 'success').then(function () {
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

        /* 参与众筹 */
        $('.btn-crowdfunding').on('click', function () {
            // 判断是否选中 SKU
            if (!$('label.active input[name=sku]').val()) {
                swal('请选择商品');
                return
            }
            var address ={!! json_encode(Auth::check() ? Auth::user()->address : []) !!}
            var $form = $('<form></form>');
            $form.append('<div class="form-group row">' +
                '<label class="col-form-label col-sm-3">选择地址</label>' +
                '<div class="col-sm-9">' +
                '<select class="custom-select" name="address_id"></select>' +
                '</div></div>');
            // 循环每个收货地址
            address.forEach(function (address) {
                $form.find('select[name=address_id]').append('<option value="' + address.id + '">' +
                    address.full_address + '' + address.contact_name + '' + address.contact_phone +
                    '</option>')
            });

            // 在表单中添加一个名为 购买数量 的输入框
            $form.append('<div class="form-group row">' +
                '<label class="col-form-label col-sm-3">够买数量</label>' +
                '<div class="col-sm-9"><input type="text" class="form-control" name="amount">' +
                '</div></div>');
            // 调用 SweetAlert 弹框
            swal({
                text: '参与众筹',
                content: $form[0],
                buttons: ['取消', '确定']
            }).then(function (ret) {
                if (!ret) {
                    return
                }
                // 构建请求参数
                var req = {
                    address_id: $form.find('select[name=address_id]').val(),
                    amount: $form.find('input[name=amount]').val(),
                    sku_id: $('label.active input[name=sku]').val()
                };
                // 调用众筹商品下单接口
                axios.post('{{ route('crowdfunding_orders.store') }}', req)
                    .then(function (response) {
                        // 订单创建成功，跳转到订单详情页
                        swal('订单提交成功', '', 'success').then(() => {
                            location.href = '/orders/' + response.data.id
                        });
                    }, function (error) {
                        if (error.response.status === 422) {
                            var html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error + '<br>'
                                })
                            });
                            html += '</div>';
                            swal({content: $(html)[0], icon: 'error'})
                        } else if (error.response.status === 403) {
                            swal(error.response.data.msg, '', 'error')
                        } else {
                            swal('系统错误', '', 'error')
                        }
                    })
            })
        });
    </script>
@endsection
