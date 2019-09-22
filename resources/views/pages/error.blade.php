@extends('layouts.app')
@section('title', '支付失败')

@section('content')
    <div class="card">
        <div class="card-header">操作失败</div>
        <div class="card-body text-center">
            <h1>{{ $msg }}</h1>
            <a href="{{ route('home') }}" class="btn btn-primary">返回首页</a>
            <a href="{{ route('orders.index') }}" class="btn btn-primary">返回订单中心</a>
        </div>
    </div>
@endsection
