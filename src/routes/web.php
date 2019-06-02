<?php

Route::group(['namespace' => 'Dorcas\ModulesServiceRequests\Http\Controllers', 'middleware' => ['web','auth']], function() {
    Route::get('service-requests-main', 'ModulesServiceRequestsController@index')->name('service-requests-main');
});


?>