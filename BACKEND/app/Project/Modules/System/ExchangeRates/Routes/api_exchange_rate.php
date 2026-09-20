<?php

use App\Project\Modules\System\ExchangeRates\ApiControllers\ExchangeRateController;
use Illuminate\Support\Facades\Route;

Route::controller(ExchangeRateController::class)->group(function () {
    Route::post('exchange-rate', action: 'getRate')->name('exchange-rate');
});
