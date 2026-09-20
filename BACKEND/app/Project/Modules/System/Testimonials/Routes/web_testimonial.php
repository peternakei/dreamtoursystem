<?php

use App\Project\Modules\System\Testimonials\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('testimonials')->controller(TestimonialController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('testimonials.change_status');
    });

    Route::resources([
        'testimonials' => TestimonialController::class
    ]);
});
