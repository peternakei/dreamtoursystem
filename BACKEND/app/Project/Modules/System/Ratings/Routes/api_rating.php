<?php

use App\Project\Modules\System\Ratings\ApiControllers\RateAndReviewController;
use Illuminate\Support\Facades\Route;

Route::controller(RateAndReviewController::class)->group(function () {
    Route::post('submit_rating', 'submitRating')->name('rating.submit')->middleware('auth:sanctum');
    Route::put('ratings/{rating_uuid}', 'updateRating')->name('rating.update')->middleware('auth:sanctum');
    Route::delete('ratings/{rating_uuid}', 'deleteRating')->name('rating.delete')->middleware('auth:sanctum');
    Route::get('user/ratings', 'getUserRatings')->name('rating.get')->middleware('auth:sanctum');
    Route::get('user/check-rating-{type}', 'checkRatingStatus')->name('rating.check')->middleware('auth:sanctum');
});
