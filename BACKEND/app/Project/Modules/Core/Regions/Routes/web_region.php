<?php

use App\Project\Modules\Core\Regions\RegionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('regions')->controller(RegionController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('regions.change_status');
    });

    Route::resources([
        'regions' => RegionController::class
    ]);
});
