<?php

use App\Project\Modules\System\ExchangeRates\ExchangeRateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('exchange_rates')->controller(ExchangeRateController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('exchange_rates.change_status');
    });

    Route::resources([
        'exchange_rates' => ExchangeRateController::class
    ]);
});
