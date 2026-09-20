<?php

use App\Project\Modules\System\Inquiries\ApiControllers\ServiceInquiryController;
use Illuminate\Support\Facades\Route;

Route::post('save-service-inquiry', [ServiceInquiryController::class, 'store'])->middleware('throttle:30,1')->name('save-service-inquiry');
Route::middleware('auth:sanctum')->controller(ServiceInquiryController::class)->group(function () {
    Route::get('get-service-inquiries', 'index')->name('get-service-inquiries');
    Route::get('get-service-inquiry/{uuid}', 'show')->whereUuid('uuid')->name('get-service-inquiry');
    Route::post('service-inquiries/{uuid}/request-cancellation', 'requestCancellation')->whereUuid('uuid')->name('service-inquiry.cancellation');
});
