<?php

use App\Project\Modules\System\Dashboard\ApiControllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::controller(DashboardController::class)->group(function () {
    Route::get('user/dashboard', 'getUserDashboard')->name('dashboard.get')->middleware('auth:sanctum');
});
