<?php

use App\Project\Modules\System\Tourists\TouristController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('tourists')->controller(TouristController::class)->group(function () {
        Route::get('active', 'active')->name('tourists.active');
        Route::get('inactive', 'inactive')->name('tourists.inactive');
        Route::post('change_status/{id}', 'changeStatus')->name('tourists.change_status');
    });

    Route::resources([
        'tourists' => TouristController::class
    ]);
});
