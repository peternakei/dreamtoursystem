<?php

use App\Project\Modules\System\Destinations\ApiControllers\DestinationController;
use Illuminate\Support\Facades\Route;

Route::controller(DestinationController::class)->group(function () {
    Route::get('destinations', 'getDestination')->name('destinations.index');
    Route::post('destinations/{id}', 'getDestinationDetails')->name('destinations.details');
});
