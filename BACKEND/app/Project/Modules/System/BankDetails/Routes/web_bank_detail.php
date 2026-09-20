<?php

use App\Project\Modules\System\BankDetails\BankDetailController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('bank_details')->controller(BankDetailController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('bank_details.change_status');
    });

    Route::resources([
        'bank_details' => BankDetailController::class
    ]);
});
