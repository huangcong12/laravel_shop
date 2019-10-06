<?php

namespace App\Console\Commands\Cron;

use App\CrowdfundingProduct;
use App\Jobs\RefundCrowdfundingOrders;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FinishCrowdfunding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:finish-crowdfunding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结束众筹';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        CrowdfundingProduct::query()
            // 众筹时间结束
            ->where('end_at', '<=', Carbon::now())
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->get()
            ->each(function (CrowdfundingProduct $crowdfundingProduct) {
                if ($crowdfundingProduct->target_amount > $crowdfundingProduct->total_amount) {
                    // 众筹失败
                } else {
                    // 众筹成功
                    $this->crowdfundingSuccess($crowdfundingProduct);
                }
            });
    }

    /**
     * 众筹成功
     *
     * @param CrowdfundingProduct $crowdfunding
     */
    public function crowdfundingSuccess(CrowdfundingProduct $crowdfunding)
    {
        return $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_SUCCESS
        ]);
    }

    /**
     * 众筹失败
     *
     * @param CrowdfundingProduct $crowdfunding
     */
    public function crowdfundingFail(CrowdfundingProduct $crowdfunding)
    {
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_FAIL
        ]);

        // 使用定时任务触发退款
        dispatch(new RefundCrowdfundingOrders($crowdfunding));
    }
}
