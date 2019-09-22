<?php

namespace App\Listeners;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Notification;

class OrderPaidNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * 选择通知方式
     *
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * 发送提醒邮件
     *
     * @param $notifyable
     */
    public function toMail($notifyable)
    {
        return (new MailMessage())
            ->subject('订单支付成功')     // 邮件标题
            ->greeting($this->order->user->name . '您好') // 欢迎词
            ->line('您于' . $this->order->created_at->format('m-d H:i') . '创建的订单已支付成功') // 邮件内容
            ->action('查看订单', route('orders.show', [$this->order->id]))  // 邮件中的按钮对应的链接
            ->success();        // 按钮色调
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        //
    }
}
