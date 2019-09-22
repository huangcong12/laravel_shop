<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\OrderItem;

class UpdateProductSoldCount
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param OrderPaid $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();

        $order->load('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;

            // 全表遍历已卖出的商品
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');
                })->sum('amount');

            // 更新商品数量
            $product->update([
                'sold_count' => $soldCount
            ]);
        }
    }
}
