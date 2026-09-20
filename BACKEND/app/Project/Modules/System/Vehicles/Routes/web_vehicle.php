<?php

use App\Project\Modules\System\Vehicles\VehicleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::prefix('vehicles')->controller(VehicleController::class)->group(function () {
        Route::get('/', 'index')->name('vehicles.index');
        Route::get('active', 'active')->name('vehicles.active');
        Route::get('inactive', 'inactive')->name('vehicles.inactive');
        Route::post('/', 'store')->name('vehicles.store');
        Route::get('{vehicle}', 'show')->name('vehicles.show');
        Route::put('{vehicle}', 'update')->name('vehicles.update');
        Route::post('change_status/{vehicle}', 'changeStatus')->name('vehicles.change_status');
        Route::delete('{vehicle}', 'destroy')->name('vehicles.destroy');
    });
});
