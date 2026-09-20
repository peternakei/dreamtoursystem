<?php

use App\Project\Modules\Core\Locations\LocationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('locations')->controller(LocationController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('locations.change_status');
    });

    Route::resources([
        'locations' => LocationController::class
    ]);
});
