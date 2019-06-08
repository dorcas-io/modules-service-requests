<?php

Route::group(['namespace' => 'Dorcas\ModulesServiceRequests\Http\Controllers', 'middleware' => ['web','auth'], 'prefix' => 'mps'], function() {
    Route::get('service-requests-main', 'ModulesServiceRequestsController@index')->name('service-requests-main')->middleware('professional_only');
    Route::post('service-requests-main', 'ModulesServiceRequestsController@post')->middleware('professional_only');
    
    Route::get('/service-requests', 'ModulesServiceRequestsController@getServiceRequests');
    Route::put('/service-requests/{id}', 'ModulesServiceRequestsController@updateServiceRequest');
});


?>