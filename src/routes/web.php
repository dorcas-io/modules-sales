<?php

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
    Route::put('/sales-product-barcode/{id}', 'ModulesSalesController@product_update_barcode');
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

    Route::post('/sales-variant-post', 'ModulesSalesController@variant_post')->name('sales-variant-post');
    Route::get('/sales-variant-type', 'ModulesSalesController@variant_type_get')->name('sales-variant-type-get');
    Route::post('/sales-variant-type', 'ModulesSalesController@variant_type_set')->name('sales-variant-type-set');
    Route::post('/sales-variant-type-remove', 'ModulesSalesController@variant_type_remove')->name('sales-variant-type-remove');

    Route::get('/sales-logistics', 'ModulesSalesController@logistics')->name('sales-logistics');

    Route::get('/sales-logistics-provider', 'ModulesSalesController@logistics_provider')->name('sales-logistics-provider');

    Route::get('/sales-logistics-fulfilment', 'ModulesSalesController@logistics_fulfilment')->name('sales-logistics-fulfilment');

    Route::get('/sales-shipping-routes', 'ModulesSalesController@shipping_routes')->name('sales-shipping-routes');
    Route::post('/sales-shipping-routes', 'ModulesSalesController@shipping_routes_post')->name('sales-shipping-routes-post');

    Route::get('/sales-report-routes', 'ModulesSalesController@salesReport')->name('sales-report-routes');
    Route::post('/sales-report-generate', 'ModulesSalesController@generateSalesReport')->name('sales-report-generate');
    

	//Invoices
	Route::get('/sales-invoices', 'ModulesSalesController@invoices_index')->name('sales-invoices');
    Route::get('/invoices/{id}', 'ModulesSalesController@invoices_generate')->name('invoice-generate');
	// Route::get('/orders', 'Orders@index')->name('apps.invoicing.orders');


	//orders
	Route::get('/sales-orders', 'ModulesSalesController@orders_index')->name('sales-orders');
    Route::get('/sales-orders-search', 'ModulesSalesController@orders_search')->name('sales-orders-search');
    Route::get('/sales-orders-new', 'ModulesSalesController@order_new')->name('sales-orders-new');
    Route::post('/sales-orders-new', 'ModulesSalesController@order_create');
    Route::get('/sales-order/{id}', 'ModulesSalesController@order_index')->name('sales-orders-single');
    Route::put('/sales-order/{id}', 'ModulesSalesController@order_update');
    Route::put('/sales-order-status/{id}', 'ModulesSalesController@order_status_update');
    Route::delete('/sales-order/{id}', 'ModulesSalesController@order_delete');
    Route::put('/sales-order/{id}/customers', 'ModulesSalesController@order_updateCustomerOrder');
    Route::delete('/sales-order/{id}/customers', 'ModulesSalesController@order_deleteCustomer');


    Route::post('/map-category','ModulesSalesController@mapCategory');
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