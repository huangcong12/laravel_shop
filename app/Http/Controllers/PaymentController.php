<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
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
}
