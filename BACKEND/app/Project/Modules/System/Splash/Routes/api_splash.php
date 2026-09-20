<?php

use App\Project\Modules\System\Splash\ApiControllers\SplashController;
use Illuminate\Support\Facades\Route;

Route::controller(SplashController::class)->group(function () {
    Route::get('splash', 'getSplash')->name('splash');
});
