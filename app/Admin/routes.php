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
    $router->post('/users/{id}', 'UsersController@update');

    // 显示
    $router->get('/users/{id}', 'UsersController@show');

    /**
     * 商品列表
     */
    Route::get('products', 'ProductsController@index');


});
