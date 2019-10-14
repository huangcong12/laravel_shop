<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Installment;
use App\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yansongda\Supports\Log;

class PaymentController extends Controller
{
    /**
     * 支付宝回调
     *
     * @param Order $order
     * @param Request $request
     * @return mixed
     * @throws InvalidRequestException
     * @throws AuthorizationException
     */
    public function payByAlipay(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        // 非可正常支付订单
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        return app('alipay')->web([
            'out_trade_no' => $order->no,
            'total_amount' => $order->total_amount,
//            'total_amount' => 0.01,
            'subject' => '支付 Laravel Shop 订单：' . $order->no,
        ]);
    }


    /**
     * 支付宝支付页面回跳
     */
    public function alipayReturn()
    {
        try {
            $data = app('alipay')->verify();
        } catch (Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }


    public function alipayNotify()
    {
        try {
            $data = app('alipay')->verify();
            if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return app('alipay')->success();
            }

            $order = Order::where('no', $data->out_trade_no)->first();
            if (!$order) {
                throw new Exception('订单号有误，不能查询到有效订单');
            }
            if ($order->paid_at) {
                return app('alipay')->success();
            }

            $order->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'alipay',
                'payment_no' => $data->trade_no,
            ]);

            $this->afterPaid($order);

            return app('alipay')->success();

        } catch (Exception $e) {
            Log::info('支付宝支付回调有误：' . $e->getMessage());
            return 'fail';
        }
    }

    /**
     * 支付成功事件
     *
     * @param $order
     */
    public function afterPaid($order)
    {
        event(new OrderPaid($order));
    }

    /**
     * 分期付款
     *
     * @param Order $order
     * @param Request $request
     */
    public function payByInstallment(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 订单不满足最低分期要求
        if ($order->total_amount < config('app.min_installment_amount')) {
            throw new InvalidRequestException('订单金额低于最低分期金额');
        }

        // 校验用户提交的还款月数，数值必须是我们配置好费率的期数
        $this->validate($request, [
            'count' => ['required', Rule::in(array_keys(config('app.installment_fee_rate')))]
        ]);

        // 删除同一笔商品订单发起过的其他状态是未支付的分期付款，避免同一笔商品订单有多个分期付款
        // TODO 这里有点蒙圈，搞不懂这里什么时候会出现这种情况
        Installment::query()
            ->where('order_id', $order->id)
            ->where('status', Installment::STATUS_PENDING)
            ->delete();
        $count = $request->input('count');
        // 创建一个新的分期对象
        $installment = new Installment([
            // 总本金即为商品订单总金额
            'total_amount' => $order->total_amount,
            // 分期期数
            'count' => $count,
            // 从配置文件中读取相应期数的费率
            'fee_rate' => config('app.installment_fee_rate')[$count],
            // 从配置文件中读取当期逾期费率
            'fine_rate' => config('app.installment_fine_rate'),
        ]);
        $installment->user()->associate($request->user());
        $installment->order()->associate($order);
        $installment->save();

        // 第一期还款截止时间为明天凌晨 0 点
        $dueDate = Carbon::tomorrow();
        // 甲酸每一期的本金
        $base = big_number($order->total_amount)->divide($count)->getValue();
        // 计算每一期的手续费
        $fee = big_number($base)->multiply($installment->fee_rate)->divide(100)->getValue();

        // 根据用户选择的还款期数，创建对应数量的还款计划
        for ($i = 0; $i < $count; $i++) {
            // 最后一期的本金需要用总本金减去前面几期的本金
            if ($i === $count - 1) {
                $base = big_number($order->total_amount)->subtract(big_number($base)->multiply($count - 1));
            }
            $installment->items()->create([
                'sequence' => $i,
                'base' => $base,
                'fee' => $fee,
                'due_date' => $dueDate
            ]);
            // 还款截止日期加上 30 天
            $dueDate = $dueDate->copy()->addDay(30);
        }

        return $installment;

    }
}
