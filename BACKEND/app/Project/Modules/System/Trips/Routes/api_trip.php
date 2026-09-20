<?php

use App\Project\Modules\System\Trips\ApiControllers\TripController;
use Illuminate\Support\Facades\Route;

Route::controller(TripController::class)->group(function () {
    Route::get('trips', 'getTrips')->name('trips.index');
    Route::post('trips/{id}', 'getTripDetails')->name('trips.details');
    Route::post('search_trip', 'searchTrip')->name('search_trip');
});
