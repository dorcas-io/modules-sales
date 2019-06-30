<?php

/*use GuzzleHttp\Psr7\Uri;
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
*/


Route::group(['namespace' => 'Dorcas\ModulesSales\Http\Controllers', 'prefix' => 'msl', 'middleware' => ['web','auth']], function() {

	//categories
	Route::get('/sales-categories', 'ModulesSalesController@categories_index')->name('sales-categories');
	Route::post('/sales-categories', 'ModulesSalesController@categories_create');
	Route::delete('/sales-categories/{id}', 'ModulesSalesController@categories_delete');
	Route::put('/sales-categories/{id}', 'ModulesSalesController@categories_update');

	//products
	Route::get('/sales-products', 'ModulesSalesController@products_index')->name('sales-products');
	Route::get('/sales-products-search','ModulesSalesController@products_search')->name('sales-product-search');
	Route::post('/sales-products', 'ModulesSalesController@product_create');

	Route::get('/sales-product/{id}', 'ModulesSalesController@product_index')->name('sales-products-single');
    Route::put('/sales-product/{id}', 'ModulesSalesController@product_update');
    Route::delete('/sales-product/{id}', 'ModulesSalesController@product_delete');
    Route::post('/sales-product/{id}/images', 'ModulesSalesController@product_addImage')->name('sales-product-single-images');
    Route::get('/sales-product/{id}/images', 'ModulesSalesController@product_redirect');
    Route::delete('/sales-product/{id}/images', 'ModulesSalesController@product_deleteImage');
    Route::post('/sales-product/{id}/stocks', 'ModulesSalesController@product_updateStocks')->name('sales-product-single-stocks');
    //Route::get('/sales-product/{id}/stocks', 'ModulesSalesController@product_redirect');
    Route::get('/sales-product/{id}/stocks', 'ModulesSalesController@product_stocks');
    Route::post('/sales-products/{id}/categories', 'ModulesSalesController@product_addCategories')->name('sales-product-single-categories');
    Route::get('/sales-product/{id}/categories', 'ModulesSalesController@product_redirect');
    Route::delete('/sales-product/{id}/categories', 'ModulesSalesController@product_deleteCategory');




	//Invoices
	Route::get('/sales-invoices', 'ModulesSalesController@invoices_index')->name('sales-invoices');
	//Route::get('/orders', 'Orders@index')->name('apps.invoicing.orders');


	//orders
	Route::get('/sales-orders', 'ModulesSalesController@orders_index')->name('sales-orders');
    Route::get('/sales-orders-search', 'ModulesSalesController@orders_search')->name('sales-orders-search');
    Route::get('/sales-orders-new', 'ModulesSalesController@order_new')->name('sales-orders-new');
    Route::post('/sales-orders-new', 'ModulesSalesController@order_create');
    Route::get('/sales-order/{id}', 'ModulesSalesController@order_index')->name('sales-orders-single');
    Route::put('/sales-order/{id}', 'ModulesSalesController@order_update');
    Route::delete('/sales-order/{id}', 'ModulesSalesController@order_delete');
    Route::put('/sales-order/{id}/customers', 'ModulesSalesController@order_updateCustomerOrder');
    Route::delete('/sales-order/{id}/customers', 'ModulesSalesController@order_deleteCustomer');


});


/*
    Route::get('/categories', 'Categories@index')->name('apps.inventory.categories');
    
    Route::get('/products', 'Products@index')->name('apps.inventory');
    Route::post('/products', 'Products@create');
    Route::get('/products/import', 'Products@index')->name('apps.inventory.import');
    Route::get('/products/new', 'Products@index')->name('apps.inventory.new');
    


    Route::get('/orders', 'Orders@index')->name('apps.invoicing.orders');
    Route::get('/orders/{id}', 'Order@index')->name('apps.invoicing.orders.single');



    */



/* 


    Route::get('/inventory/products', 'Inventory\Products@search');
    
    
    


    Route::get('/inventory/products/{id}/stocks', 'Inventory\Products@stocks');

    

    */




?>