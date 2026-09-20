<?php

use App\Project\Modules\System\Ratings\RatingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('ratings')->controller(RatingController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('ratings.change_status');
    });

    Route::resources([
        'ratings' => RatingController::class
    ]);
});
