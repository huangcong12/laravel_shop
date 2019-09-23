@extends('layouts.app')
@section('title', '订单列表')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">订单列表</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($orders as $order)
                            <li class="list-group-item">
                                <div class="card">
                                    <div class="card-header">
                                        订单号：{{ $order->no }}
                                        <span class="float-right">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>商品信息</th>
                                                <th class="text-center">单价</th>
                                                <th class="text-center">数量</th>
                                                <th class="text-center">订单总价</th>
                                                <th class="text-center">状态</th>
                                                <th class="text-center">操作</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($order->items as $index=>$item)
                                                <tr>
                                                    <td class="product-info">
                                                        <div class="preview">
                                                            <a href="{{ route('products.show', [$item->product_id]) }}"
                                                               target="_blank">
                                                                <img src="{{ $item->product->image_url }}" alt="">
                                                            </a>
                                                        </div>
                                                        <div>
                                                            <span class="product-title">
                                                                <a href="{{ route('products.show', [$item->product_id]) }}"
                                                                   target="_blank">
                                                                    {{ $item->product->title }}
                                                                </a>
                                                            </span>
                                                            <span
                                                                class="sku-title">{{ $item->productSku->title }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="sku-price text-center">￥{{ $item->price }}</td>
                                                    <td class="sku-amount text-center">{{ $item->amount }}</td>
                                                    @if($index === 0)
                                                        <td rowspan="{{ count($order->items) }}"
                                                            class="text-center total-amount">
                                                            ￥ {{ $order->total_amount }}</td>
                                                        <td rowspan="{{ count($order->items) }}" class="text-center">
                                                            @if($order->paid_at)
                                                                @if($order->refund_status === \App\Order::REFUND_STATUS_PENDING)
                                                                    已支付
                                                                @else
                                                                    {{ \App\Order::$refundStatusMap[$order->refund_status] }}
                                                                @endif
                                                            @elseif($order->closed)已关闭
                                                            @else 未支付<br>
                                                            请于{{ $order->created_at->addSeconds(config('app.order_ttl'))->format('H:i') }}
                                                            前完成支付<br>
                                                            否则订单将自动关闭
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ count($order->items) }}" class="text-center">
                                                            <a href="{{ route('orders.show', ['order'=>$order->id]) }}"
                                                               class="btn btn-primary btn-sm">查看订单</a>
                                                            @if($order->paid_at)
                                                                <a href="{{ route('orders.review.show', [$order->id]) }}"
                                                                   class="btn btn-sm btn-success">{{ $order->reviewed ? '查看评价' : '评价' }}</a>
                                                            @elseif(!$order->paid_at && !$order->closed)
                                                                <a href="{{ route('payment.alipay', ['order'=>$order->id]) }}"
                                                                   class="btn btn-primary btn-sm">支付宝支付</a>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="float-right">{{ $orders->render() }}</div>
                </div>
            </div>
        </div>

    </div>
@endsection
