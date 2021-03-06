<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    /**
     * 用户列表
     */
    $router->get('/', 'HomeController@index')->name('admin.home');

    // 新增
    $router->get('/users/create', 'UsersController@create');
    $router->post('/users', 'UsersController@store');

    // 用户列表
    $router->get('/users', 'UsersController@index');

    // 编辑
    $router->get('/users/{id}/edit', 'UsersController@edit');
    $router->put('/users/{id}', 'UsersController@update');

    // 显示
    $router->get('/users/{id}', 'UsersController@show');

    /**
     * 商品
     */
    /* 列表 */
    Route::get('products', 'ProductsController@index');
    // 新增
    Route::get('products/create', 'ProductsController@create');
    Route::post('products', 'ProductsController@store');

    // 编辑
    Route::get('products/{id}/edit', 'ProductsController@edit');
    Route::put('products/{id}', 'ProductsController@update');

    // 显示
    $router->get('/products/{id}', 'ProductsController@show');

    /**
     * 订单
     */
    // 列表页
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    // 详情
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    // 增加物流信息
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
    // 退款
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.refund');

    /**
     * 优惠券
     */
    $router->resource('coupon_codes', 'CouponCodesController');

    /**
     * 商品类目
     */
    $router->resource('categories', CategoriesController::class);
    $router->get('api/categories', 'CategoriesController@apiIndex');

    /**
     * 众筹商品
     */
    $router->resource('crowdfunding_products', CrowdfundingProductsController::class);

});
