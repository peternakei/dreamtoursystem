<?php

use App\Project\Modules\Core\Districts\DistrictController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('districts')->controller(DistrictController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('districts.change_status');
    });

    Route::resources([
        'districts' => DistrictController::class
    ]);
});
