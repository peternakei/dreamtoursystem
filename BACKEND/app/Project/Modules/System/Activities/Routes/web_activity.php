<?php

use App\Project\Modules\System\Activities\ActivityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('activities')->controller(ActivityController::class)->group(function () {
        Route::post('create_price/{id}', 'createPrice')->name('activities.create_price');
        Route::post('change_status/{id}', 'changeStatus')->name('activities.change_status');
    });

    Route::resources([
        'activities' => ActivityController::class
    ]);
});
