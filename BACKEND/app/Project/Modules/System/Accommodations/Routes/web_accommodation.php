<?php

use App\Project\Modules\System\Accommodations\AccommodationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::prefix('accommodations')->controller(AccommodationController::class)->group(function () {
        Route::get('/', 'index')->name('accommodations.index');
        Route::get('active', 'active')->name('accommodations.active');
        Route::get('inactive', 'inactive')->name('accommodations.inactive');
        Route::post('/', 'store')->name('accommodations.store');
        Route::get('{accommodation}', 'show')->name('accommodations.show');
        Route::put('{accommodation}', 'update')->name('accommodations.update');
        Route::post('change_status/{accommodation}', 'changeStatus')->name('accommodations.change_status');
        Route::delete('{accommodation}', 'destroy')->name('accommodations.destroy');
    });
});
