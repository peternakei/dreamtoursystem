<?php

use App\Project\Modules\System\Subscriptions\ApiControllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::controller(SubscriptionController::class)->group(function () {
    Route::post('subscribe', 'saveSubscription')->name('subscribe');
});
