<?php

namespace App\Jobs;

use App\Order;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay(Carbon::now()->addSecond($delay));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->order->paid_at) {
            return;
        }

        DB::transaction(function () {
            $this->order->update(['closed' => true]);

            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }

            if ($this->order->couponCode()) {
                $this->order->couponCode->changeUsed(false);
            }
        });
    }
}
