<?php

use App\Project\Modules\System\Refunds\RefundController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('refunds')->controller(RefundController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('refunds.change_status');
    });

    Route::resources([
        'refunds' => RefundController::class
    ]);
});
