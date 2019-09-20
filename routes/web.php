<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function () {

    // 列表
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
    // 显示新增页面
    Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
    // 保存新增数据
    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
    // 显示编辑页面
    Route::get('user_addresses/{userAddress}', 'UserAddressesController@edit')->name('user_addresses.edit');
    // 保存编辑信息
    Route::put('user_addresses/{userAddress}', 'UserAddressesController@update')->name('user_addresses.update');

    // 删除
    Route::delete('user_addresses/{userAddress}', 'UserAddressesController@destroy')->name('user_addresses.destroy');

    // 展示收藏的商品页
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
    // 收藏商品
    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
    // 取消收藏
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');

    // 展示添加购物车
    Route::get('cart', 'CartController@index')->name('cart.index');
    // 商品添加到购物车里
    Route::post('cart', 'CartController@add')->name('cart.add');
    // 移除购物车里的商品
    Route::delete('cart/{cartItem}', 'CartController@remove')->name('cart.remove');

    // 订单页
    Route::get('orders', 'OrdersController@index')->name('orders.index');
    // 下单
    Route::post('orders', 'OrdersController@store')->name('orders.store');
    // 订单详情页
    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');

});

Route::get('/', 'ProductsController@index')->name('products.index');
// 详情页
Route::get('products/{product}', 'ProductsController@show')->name('products.show');


