<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Order;
use App\Services\OrderService;
use App\UserAddress;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    /**
     * 订单页
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * 订单详情页
     *
     * @param Order $order
     * @param Request $request
     */
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }


    /**
     * 生成订单
     *
     * @param OrderRequest $request
     * @param OrderService $orderService
     */
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->get('remark'), $request->get('items'));
    }

    /**
     * 收货
     *
     * @param Order $order
     * @param Request $request
     */
    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        if ($order->ship_status != Order::SHIP_STATUS_DELIVERED) {
            throw  new InvalidRequestException('发货状态不正确');
        }

        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return redirect()->back();
    }

    /**
     * 显示评价页面
     *
     * @param Order $order
     * @param Request $request
     */
    public function review(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，暂不可评价');
        }

        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    /**
     * 保存评价信息
     *
     * @param Order $order
     * @param Request $request
     */
    public function sendReview(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，暂不可评价');
        } elseif ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价');
        }
        $reviews = $request->input('reviews');

        DB::transaction(function () use ($reviews, $order) {
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评价和评分
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }

            // 订单标记为已评价
            $order->update(['reviewed' => true]);

            // 修改商品评分事件
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
}
