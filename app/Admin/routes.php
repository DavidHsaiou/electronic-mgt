<?php

use Encore\Admin\Admin;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('storage-areas', StorageAreaController::class);
    $router->resource('eletronics', ElectronicController::class);
    $router->resource('stock-in-records', StockInController::class);
    $router->resource('bill-types', BillTypeController::class);
    $router->resource('stock-out-types', StockOutTypeController::class);
    $router->resource('sell-channels', SellChannelController::class);
    $router->resource('shipping-types', ShippingTypeController::class);
    $router->resource('stock-out-records', StockOutRecordController::class);
    $router->resource('work-states', WorkStateController::class);
    $router->resource('electronic-types', ElectronicTypeController::class);
});
