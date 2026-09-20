<?php

use App\Project\Modules\System\Bookings\BookingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('bookings')->controller(BookingController::class)->group(function () {
        Route::get('completed', 'completed')->name('bookings.completed');
        Route::get('reserved', 'reserved')->name('bookings.reserved');
        Route::get('confirmed', 'confirmed')->name('bookings.confirmed');
        Route::get('cancelled', 'cancelled')->name('bookings.cancelled');
        Route::get('expired', 'expired')->name('bookings.expired');
        Route::post('change_status/{id}', 'changeStatus')->name('bookings.change_status');
    });

    Route::resources([
        'bookings' => BookingController::class
    ]);
});
