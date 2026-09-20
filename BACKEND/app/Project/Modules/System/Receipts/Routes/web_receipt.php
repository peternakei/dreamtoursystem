<?php

use App\Project\Modules\System\Receipts\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('receipts')->controller(ReceiptController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('receipts.change_status');
    });

    Route::resources([
        'receipts' => ReceiptController::class
    ]);
});
