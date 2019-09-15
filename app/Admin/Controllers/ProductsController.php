<?php

namespace App\Admin\Controllers;

use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Product';

    /**
     * 商品列表
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $this->title = '商品列表';
        return parent::index($content);
    }

    /**
     * 新增商品
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $this->title = '新增商品';
        return parent::create($content);
    }

    /**
     * 编辑商品
     *
     * @param mixed $id
     * @param Content $content
     * @return Content|void
     */
    public function edit($id, Content $content)
    {
        $this->title = '编辑商品';
        return parent::edit($id, $content);
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);
        $grid->column('id', __('Id'))->sortable();
        $grid->column('title', __('admin.title'));
        $grid->column('description', __('admin.description'));
        $grid->column('image', __('admin.image'));
        $grid->column('on_sale', __('admin.on_sale'));
        $grid->column('rating', __('admin.rating'));
        $grid->column('sold_count', __('admin.sold_count'));
        $grid->column('review_count', __('admin.review_count'));
        $grid->column('price', __('admin.price'));
        $grid->column('created_at', __('admin.created_at'));
        $grid->column('updated_at', __('admin.updated_at'));

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
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $form->text('title', __('admin.title'))->rules('required');
        $form->image('image', __('admin.image'))->rules('required');
        $form->textarea('description', __('admin.description'))->rules('required');
        $form->switch('on_sale', __('admin.on_sale'))
            ->states(['on' => ['text' => '是'], 'off' => ['text' => '否']]);
        $form->decimal('rating', __('admin.rating'))->default(5.00);
        $form->number('sold_count', __('admin.sold_count'))->min(0);
        $form->number('review_count', __('admin.review_count'))->min(0);
//        $form->decimal('price', __('admin.price'))->rules('required');

        // 直接添加一对多模型
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '库存')->rules('required|integer|min:0.01');
        });

        // 定义事件回调，当模型即将保存时会触发
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });

        return $form;
    }
}
