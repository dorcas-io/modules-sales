<?php

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$request = app()->make('request');
$currentHost = $request->header('host');
$defaultUri = new Uri(config('app.url'));
try {
    $domainInfo = (new App\Http\Middleware\ResolveCustomSubdomain())->splitHost($currentHost);
} catch (RuntimeException $e) {
    $domainInfo = null;
}
$storeSubDomain = !empty($domainInfo) && $domainInfo->getService() === 'store' ?
    $currentHost : 'store' . $defaultUri->getHost();



Route::group(['namespace' => 'Dorcas\ModulesSales\Http\Controllers', 'prefix' => 'msl', 'middleware' => ['web']], function() {

	//categories
	Route::get('/sales-categories', 'ModulesSalesController@categories_index')->name('sales-categories');
	Route::post('/sales-categories-create', 'ModulesSalesController@categories_create')->name('sales-categories-create');


	//products
	Route::get('/sales-products', 'ModulesSalesController@products_index')->name('sales-products');
	Route::post('/sales-products', 'ModulesSalesController@product_create');
	Route::get('/product-lists','ModulesSalesController@product_lists')->name('product-lists');
	Route::get('/sales-product/{id}', 'ModulesSalesController@product_index')->name('product-index');
    Route::put('/sales-product/{id}', 'ModulesSalesController@product_update');


	//Invoices
	Route::get('/sales-invoices', 'ModulesSalesController@invoices_index')->name('sales-invoices');


	//orders
	Route::get('/sales-orders', 'ModulesSalesController@orders_index')->name('sales-orders');

});








?>