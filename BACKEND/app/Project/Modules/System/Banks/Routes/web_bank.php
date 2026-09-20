<?php

use App\Project\Modules\System\Banks\BankController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('banks')->controller(BankController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('banks.change_status');
    });

    Route::resources([
        'banks' => BankController::class
    ]);
});
