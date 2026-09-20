<?php

use App\Project\Modules\System\Inquiries\ApiControllers\InquiryController;
use Illuminate\Support\Facades\Route;

Route::controller(InquiryController::class)->group(function () {
    Route::post('save-inquiry', 'saveInquiry')->name('save-inquiry'); // No auth required
    Route::get('get-inquiries', 'getInquiries')->name('get-inquiries'); // No auth required (but needs email/phone)
    Route::post('get-inquiry', 'getInquiryDetails')->name('get-inquiry')->middleware('auth:sanctum'); // Auth required
});
