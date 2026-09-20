<?php

use App\Project\Modules\System\Inquiries\InquiryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('inquiries')->controller(InquiryController::class)->group(function () {
        Route::get('processed', 'processed')->name('inquiries.processed');
        Route::get('pending', 'pending')->name('inquiries.pending');
        Route::post('change_status/{id}', 'changeStatus')->name('inquiries.change_status');
    });

    Route::resources([
        'inquiries' => InquiryController::class
    ]);
});
