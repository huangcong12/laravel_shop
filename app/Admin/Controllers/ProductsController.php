<?php

namespace App\Admin\Controllers;

use App\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends CommonProductsController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品列表';


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
        $grid->model()->with(['category']);

        $grid->column('id', 'ID')->sortable();
        $grid->column('title', '商品名称');
        $grid->column('category.name', '类目');
        $grid->column('on_sale', '已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->column('price', '价格');
        $grid->column('rating', '评分');
        $grid->column('sold_count', '销量');
        $grid->column('review_count', '评论数');
    }

    /**
     * 各个类型的控制器将实现本方法来定义表单应该有哪些额外的字段
     *
     * @param Form $form
     * @return mixed
     */
    protected function customForm(Form $form)
    {
        $form->decimal('rating', __('admin.rating'))->default(5.00);
        $form->number('sold_count', __('admin.sold_count'))->min(0);
        $form->number('review_count', __('admin.review_count'))->min(0);
    }

    /**
     * 商品类型
     *
     * @return string
     */
    public function getProductType()
    {
        return Product::TYPE_NORMAL;
    }
}
