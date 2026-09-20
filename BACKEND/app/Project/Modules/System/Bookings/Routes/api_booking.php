<?php

use App\Project\Modules\System\Bookings\ApiControllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::controller(BookingController::class)->group(function () {
    Route::post('save_booking', 'saveBooking')->name('save_booking');
    Route::get('get-bookings', 'getBookings')->name('get-bookings')->middleware('auth:sanctum');
    Route::get('get-booking-details', 'getBookingDetails')->name('get-booking-details')->middleware('auth:sanctum');
    Route::post('cancel-booking', 'cancelBooking')->name('cancel-booking')->middleware('auth:sanctum');
});
