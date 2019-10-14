<?php

namespace App\Http\Controllers;

use App\Installment;
use Illuminate\Http\Request;
use Illuminate\Queue\RedisQueue;

class InstallmentsController extends Controller
{
    /**
     * 分期付款页面
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $installments = Installment::query()
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return view('installments.index', ['installments' => $installments]);
    }

    /**
     * 详情页
     *
     * @param Installment $installment
     */
    public function show(Installment $installment)
    {
        $this->authorize('own', $installment);
        $items = $installment->items()->orderBy('sequence')->get();
        return view('installments.show', [
            'installment' => $installment,
            'items' => $items,
            // 下一个未完成还款的还款计划
            'nextItem' => $items->where('paid_at', null)->first()
        ]);
    }
}
