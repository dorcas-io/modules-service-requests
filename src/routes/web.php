<?php

Route::group(['namespace' => 'Dorcas\ModulesServiceRequests\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesServiceRequestsController@index')->name('sales');
});


?>