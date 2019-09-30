<?php

namespace App\Admin\Controllers;

use App\CrowdfundingProduct;
use App\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CrowdfundingProductsController extends CommonProductsController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '众筹商品列表';


    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('on_sale', __('On sale'));
        $show->field('rating', __('Rating'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('category_id', __('Category id'));

        return $show;
    }

    /**
     * 各个类型的控制器将实现本方法来定义列表应该展示哪些字段
     *
     * @param $grid
     * @return mixed
     */
    protected function customGrid(Grid $grid)
    {
        $grid->column('id', 'ID')->sortable();
        $grid->column('title', '商品名称');
        $grid->column('on_sale', '已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->column('price', '价格');
        $grid->column('crowdfunding.target_amount', '目标金额');
        $grid->column('crowdfunding.end_at', '结束时间');
        $grid->column('crowdfunding.total_amount', '目前金额')->display(function ($value) {
            return $value ?? 0.00;
        });
        $grid->column('crowdfunding.status', '状态')->display(function ($value) {
            return CrowdfundingProduct::$statusMap[$value];
        });
    }

    /**
     * 各个类型的控制器将实现本方法来定义表单应该有哪些额外的字段
     *
     * @param Form $form
     * @return mixed
     */
    protected function customForm(Form $form)
    {
        $form->text('crowdfunding.target_amount', '众筹目标金额')->rules('required|numeric|min:0.01');
        $form->datetime('crowdfunding.end_at', '众筹结束时间')->rules('required|date');
    }

    /**
     * 返回当前管理的商品类型
     *
     * @return mixed
     */
    public function getProductType()
    {
        return Product::TYPE_CROWDFUNDING;
    }

}
