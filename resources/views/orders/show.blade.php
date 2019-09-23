@extends('layouts.app')
@section('title', '订单详情页')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header"><h4>订单详情</h4></div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>商品信息</th>
                        <th class="text-center">单价</th>
                        <th class="text-center">数量</th>
                        <th class="text-center">小计</th>
                    </tr>
                    </thead>
                    @foreach($order->items as $index=>$item)
                        <tr>
                            <td class="product-info">
                                <div class="preview">
                                    <a href="{{ route('products.show',[$item->product_id]) }}" target="_blank">
                                        <img src="{{ $item->product->image_url }}" alt="">
                                    </a>
                                </div>
                                <div>
                                    <span class="product-title">
                                        <a href="{{ route('products.show', [$item->product_id]) }}">
                                            {{ $item->product->title }}
                                        </a>
                                    </span>
                                    <span class="sku-title">{{ $item->productSku->title }}</span>
                                </div>
                            </td>
                            <td class="sku-price text-center vartical-middle">￥{{ $item->price }}</td>
                            <td class="sku-amount text-center vartical-middle">{{ $item->amount }}</td>
                            <td class="item-amount text-right vartical-middle">
                                ￥{{ number_format($item->price * $item->amount, 2,'.', '') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                    </tr>
                </table>
                <div class="order-bottom">
                    <div class="order-info">
                        <div class="line">
                            <div class="line-label">收货地址:</div>
                            <div class="line-value">{{ join('', $order->address) }}</div>
                        </div>
                        <div class="line">
                            <div class="line-label">订单备注：</div>
                            <div class="line-value">{{ $order->remark ? : '-' }}</div>
                        </div>
                        <div class="line">
                            <div class="line-label">订单编号：</div>
                            <div class="line-value">{{ $order->no }}</div>
                        </div>
                        {{-- 物流信息 --}}
                        <div class="line">
                            <div class="line-label">物流状态：</div>
                            <div class="line-value">{{ \App\Order::$shipStatusMap[$order->ship_status] }}</div>
                        </div>
                        @if($order->ship_data)
                            <div class="line">
                                <div class="line-label">物流信息：</div>
                                <div
                                    class="line-label">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="order-summary text-right">
                        <div class="total-amount">
                            <span>订单总价：</span>
                            <div class="value">￥{{ $order->total_amount }}</div>
                        </div>
                        <div>
                            <span>订单状态：</span>
                            <div class="value">
                                @if($order->paid_at)
                                    @if($order->refund_status === \App\Order::REFUND_STATUS_PENDING)已支付
                                    @else {{ \App\Order::$refundStatusMap[$order->refund_status] }}
                                    @endif
                                @elseif($order->closed)已关闭
                                @else 未支付
                                @endif
                            </div>
                            @if(!$order->paid_at && !$order->closed)
                                <div class="payment-buttons">
                                    <a href="{{ route('payment.alipay', ['order'=>$order->id]) }}"
                                       class="btn btn-primary btn-sm">支付宝支付</a>
                                </div>
                            @endif

                            @if($order->ship_status == \App\Order::SHIP_STATUS_DELIVERED)
                                <div class="receive-button">
                                    {{--                                    <form action="{{ route('orders.received', [$order->id]) }}" method="post">--}}
                                    {{--                                        {{ csrf_field() }}--}}
                                    <button id="btn-receive" class="btn btn-sm btn-success">确认收货</button>
                                    {{--                                    </form>--}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptsAfterJs')
    <script>
        $('#btn-receive').on('click', function () {
            swal({
                title: '确认已收到商品?',
                icon: 'warning',
                dangerMode: true,
                buttons: ['取消', '确认收到']
            }).then(function (ret) {
                if (!ret) {
                    return
                }
                axios.post('{{ route('orders.received', [$order->id]) }}').then(function () {
                    location.reload()
                })
            })
        })
    </script>
@endsection
