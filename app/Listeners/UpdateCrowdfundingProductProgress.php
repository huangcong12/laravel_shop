<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Order;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCrowdfundingProductProgress implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param OrderPaid $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();

        // 如果订单不是众筹订单
        if ($order->type !== Order::TYPE_CROWDFUNDING) {
            return;
        }
        $crowdfunding = $order->items[0]->product->crowdfunding;

        $data = Order::query()
            ->where('type', Order::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->whereHas('items', function ($query) use ($crowdfunding) {
                $query->where('product_id', $crowdfunding->product_id);
            })->first([
                // 取出总订单金额
                DB::raw('sum(total_amount) as total_amount'),
                // 取出去重的支持用户u
                DB::raw('count(distinct(user_id)) as user_count')
            ]);

        return $crowdfunding->update([
            'total_amount' => $data->total_amount,
            'user_count' => $data->user_count
        ]);
    }
}
