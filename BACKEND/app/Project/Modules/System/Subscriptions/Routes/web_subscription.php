<?php

use App\Project\Modules\System\Subscriptions\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('subscriptions')->controller(SubscriptionController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('subscriptions.change_status');
    });

    Route::resources([
        'subscriptions' => SubscriptionController::class
    ]);
});
