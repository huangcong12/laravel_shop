@extends('layouts.app')
@section('title', '查看分期付款')

@section('content')
    <div class="row">
        <div class="col-10 offset-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-center">分期付款详情页</h5>
                </div>
                <div class="card-body">
                    <div class="installment-top">
                        <div class="installment-info">
                            <div class="line">
                                <div class="line line-label">分期金额：</div>
                                <div class="line-value">￥{{ $installment->total_amount }}</div>
                            </div>
                            <div class="line">
                                <div class="line line-label">分期期数：</div>
                                <div class="line-value">{{ $installment->count }}期</div>
                            </div>
                            <div class="line">
                                <div class="line line-label">分期费率：</div>
                                <div class="line-value">{{ $installment->fee_rate }}</div>
                            </div>
                            <div class="line">
                                <div class="line line-label">逾期费率：</div>
                                <div class="line-value">{{ $installment->fine_rate }}</div>
                            </div>
                            <div class="line">
                                <div class="line line-label">当前状态：</div>
                                <div class="line-value">{{ \App\Installment::$statusMap[$installment->status] }}</div>
                            </div>
                        </div>
                        <div class="installment-next text-center">
                            {{-- 如果没有还款计划，说明已经结清--}}
                            @if(is_null($nextItem))
                                <div>此订单已结清</div>
                            @else
                                <div>
                                    <span>近期待还：</span>
                                    <div class="value total-mount">￥{{ $nextItem->total }}</div>
                                </div>
                                <div>
                                    <span>截止日期：</span>
                                    <div class="value">{{ $nextItem->due_date->format('Y-m-d H:i:s') }}</div>
                                </div>
                                <div class="payment-buttons">
                                    <a href="" class="btn btn-primary">支付宝支付</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>期数</th>
                            <th>还款截止日期</th>
                            <th>状态</th>
                            <th>本金</th>
                            <th>手续费</th>
                            <th>逾期手续费</th>
                            <th class="text-center">小计</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->sequence+1 }}/{{ $installment->count }}期</td>
                                <td>{{ $item->due_date->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    {{-- 如果未付款 --}}
                                    @if(is_null($item->paid_at))
                                        @if($item->is_overdue)
                                            <span class="overdue">已逾期</span>
                                        @else
                                            <span class="needs-repay">待还款</span>
                                        @endif
                                    @else
                                        <span class="repaid">已还款</span>
                                    @endif
                                </td>
                                <td>￥{{ $item->base }}</td>
                                <td>￥{{ $item->fee }}</td>
                                <td>{{ is_null($item->fine)?'无':('￥'.$item->fine) }}</td>
                                <td class="text-center">￥{{ $item->total }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="7"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
