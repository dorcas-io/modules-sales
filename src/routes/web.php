<?php

Route::group(['namespace' => 'Dorcas\ModulesSales\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesSalesController@index')->name('sales');
});


Route::group(['middleware' => ['auth'], 'namespace' => 'Inventory', 'prefix' => 'apps/inventory'], function () {
    Route::get('/categories', 'Categories@index')->name('apps.inventory.categories');
    
    Route::get('/products', 'Products@index')->name('apps.inventory');
    Route::post('/products', 'Products@create');
    Route::get('/products/import', 'Products@index')->name('apps.inventory.import');
    Route::get('/products/new', 'Products@index')->name('apps.inventory.new');
    Route::get('/products/{id}', 'Product@index')->name('apps.inventory.single');
    Route::put('/products/{id}', 'Product@update');
    
    Route::get('/products/{id}/categories', 'Product@redirect');
    Route::post('/products/{id}/categories', 'Product@addCategories')->name('apps.inventory.single.categories');

    Route::get('/products/{id}/images', 'Product@redirect');
    Route::post('/products/{id}/images', 'Product@addImage')->name('apps.inventory.single.images');

    Route::get('/products/{id}/stocks', 'Product@redirect');
    Route::post('/products/{id}/stocks', 'Product@updateStocks')->name('apps.inventory.single.stocks');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Invoicing', 'prefix' => 'apps/invoicing'], function () {
    Route::get('/orders', 'Orders@index')->name('apps.invoicing.orders');
    Route::get('/orders/new', 'NewOrder@index')->name('apps.invoicing.orders.new');
    Route::post('/orders/new', 'NewOrder@create');
    Route::get('/orders/{id}', 'Order@index')->name('apps.invoicing.orders.single');
});

?>