<?php

Route::group(['namespace' => 'Dorcas\ModulesSales\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesSalesController@index')->name('sales');
});


?>