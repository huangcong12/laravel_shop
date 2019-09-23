<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Order';

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $this->title = '订单列表页';
        return parent::index($content);
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        $order = Order::findOrFail($id);
        return $content->header('查看订单')
            ->body(view('admin.orders.show', ['order' => $order]));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->column('no', '订单流水号');
        $grid->column('user.name', '买家');
        $grid->column('total_amount', '总金额')->sortable();
        $grid->column('paid_at', '支付时间')->sortable();
        $grid->column('ship_status', '物流')->display(function ($value) {
            return Order::$shipStatusMap[$value];
        });

        $grid->column('refund_status', '退款状态')->display(function ($value) {
            return Order::$refundStatusMap[$value];
        });

        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }


    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $order = Order::findOrFail($id);
        $show = new Show($order);

        $show->field('id', '订单 ID');
        $show->field('no', '订单编号');
        $show->field('user_id', '买家 ID');
        $show->field('user.name', '买家')->as(function ($value) use ($order) {
            return $order->user->name;
        });
        $show->field('address', '地址')->as(function ($value) {
            return $value['addresses'] . ' ' . $value['contact_name'] . ' ' . $value['contact_phone'];
        });

        $show->field('total_amount', '总价格');
        $show->field('remark', '备注');
        $show->field('paid_at', '付款时间');
        $show->field('payment_method', '支付方式');
        $show->field('payment_no', '流水号');
        $show->field('refund_status', '退款状态')->as(function ($value) {
            return Order::$refundStatusMap[$value];
        });
        $show->field('refund_no', '退款单号');
        $show->field('closed', '订单是否已关闭')->as(function ($value) {
            return $value ? '关闭' : '未关闭';
        });
        $show->field('reviewed', '是否已评价');
        $show->field('ship_status', '物流状态')->as(function ($value) {
            return Order::$shipStatusMap[$value];
        });
        $show->field('ship_data', '物流信息');
        $show->field('extra', '扩展信息');
        $show->field('created_at', '订单生成时间');
        $show->field('updated_at', '订单更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', __('No'));
        $form->number('user_id', __('User id'));
        $form->textarea('address', __('Address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->textarea('remark', __('Remark'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('refund_status', __('Refund status'))->default('pending');
        $form->text('refund_no', __('Refund no'));
        $form->switch('closed', __('Closed'));
        $form->switch('reviewed', __('Reviewed'));
        $form->text('ship_status', __('Ship status'))->default('pending');
        $form->textarea('ship_data', __('Ship data'));
        $form->textarea('extra', __('Extra'));

        return $form;
    }

    /**
     * 记录发货信息
     *
     * @param Order $order
     * @param Request $request
     */
    public function ship(Order $order, Request $request)
    {
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        } elseif ($order->ship_status != Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }

        $data = $request->validate([
            'express_company' => ['required'],
            'express_no' => ['required'],
        ], [
            'express_company' => '物流公司',
            'express_no' => '物流单号',
        ]);

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => [
                'express_company' => $request->get('express_company'),
                'express_no' => $request->get('express_no'),
            ],
        ]);

        return redirect()->back();
    }
}
