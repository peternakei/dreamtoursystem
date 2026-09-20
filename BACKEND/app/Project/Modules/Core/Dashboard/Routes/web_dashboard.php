<?php

use App\Project\Modules\Core\Dashboard\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::controller(UserDashboardController::class)->group(function () {
        Route::get('dashboard/user', 'index')->name('dashboard.user');
        Route::get('getUserDashboardData', 'getGraphsData')->name('dashboard.getUserDashboardData');
    });
});
