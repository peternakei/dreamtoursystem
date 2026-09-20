<?php

use App\Project\Modules\System\Testimonials\ApiControllers\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::controller(TestimonialController::class)->group(function () {
    Route::post('save-testimonal', 'saveTestimonial')->name('save-testimonal')->middleware('auth:sanctum');
    Route::get('get-testimonals', 'getTestimonials')->name('get-testimonals');
});
