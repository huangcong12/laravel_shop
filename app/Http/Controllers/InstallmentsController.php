<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Installment;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    /**
     * 支付宝支付
     *
     * @param Installment $installment
     */
    public function payByAlipay(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        } elseif ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该账单已被还清');
        }

        // 获取当前分期最近的需要还款计划
        if (!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()) {
            throw new InvalidRequestException('该分期订单已还清');
        }

        // 支付宝支付
        return app('alipay')->web([
            'out_trade_no' => $installment->no . '_' . $nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject' => '支付 Laravel Shop 的分期订单' . $installment->no,
            'notify_url' => route('installments.alipay.nofity'),
            'return_url' => route('installments.alipay.return')
        ]);
    }

    /**
     * 支付宝前端回跳
     */
    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $exception) {
            return view('pages.error', ['msg' => '数据不正确']);
        }
        return view('pages.success', ['msg' => '付款成功']);
    }

    /**
     * 支付宝回调
     */
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // 切割订单号，拿出订单信息
        list($no, $sequence) = explode('_', $data->out_grade_no);
        if (!$installment = Installment::where('no', $no)->first()) {
            return 'fail';
        }

        if (!$item = $installment->items()->where('sequence', $sequence)->first()) {
            return 'fail';
        }

        if ($item->paid_at) {
            return app('alipay')->success();
        }

        \DB::transaction(function () use ($data, $no, $installment, $item) {
            // 更新对应的还款计划
            $item->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'alipay',
                'payment_no' => $data->trade_no
            ]);

            // 第一笔还款
            if ($item->sequence === 0) {
                $installment->update(['status' => Installment::STATUS_PENDING]);
                // 将分期付款的对应的订单状态改成已支付
                $installment->order->update([
                    'paid_at' => Carbon::now(),
                    'payment_method' => 'installment', // 支付方式为分期付款
                    'payment_no' => $no,
                ]);
                // 触发商品订单已支付事件
                event(new OrderPaid($installment->order));
            }

            // 如果这是最后一笔付款
            if ($item->sequence === $installment->count - 1) {
                // 将分期付款改成已结清
                $installment->update(['status' => Installment::STATUS_FINISHED]);
            }
        });
        return app('alipay')->success();
    }
}
