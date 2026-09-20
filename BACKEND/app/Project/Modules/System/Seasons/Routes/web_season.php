<?php

use App\Project\Modules\System\Seasons\SeasonController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('seasons')->controller(SeasonController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('seasons.change_status');
    });

    Route::resources([
        'seasons' => SeasonController::class
    ]);
});
