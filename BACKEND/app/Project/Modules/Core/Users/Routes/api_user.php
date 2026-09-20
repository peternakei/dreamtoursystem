<?php

use App\Project\Modules\Core\Users\ApiControllers\UserInteractionController;
use Illuminate\Support\Facades\Route;

Route::controller(UserInteractionController::class)->group(function () {
    Route::get('user/check-{type}', 'checkInteractionStatus')->name('user.check')->middleware('auth:sanctum');
});
