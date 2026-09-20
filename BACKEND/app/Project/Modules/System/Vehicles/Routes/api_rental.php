<?php

use App\Project\Modules\System\Vehicles\RentalCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('rental-vehicles', [RentalCatalogController::class, 'vehicles'])->name('rental.vehicles');
Route::get('rental-offers', [RentalCatalogController::class, 'offers'])->name('rental.offers');
Route::get('rental-offers/{uuid}', [RentalCatalogController::class, 'show'])->whereUuid('uuid')->name('rental.offer');
