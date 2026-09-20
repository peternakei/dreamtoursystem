<?php

use App\Project\Modules\Core\Countries\CountryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('countries')->controller(CountryController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('countries.change_status');
    });

    Route::resources([
        'countries' => CountryController::class
    ]);
});
