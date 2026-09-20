<?php

use App\Project\Modules\System\Faqs\FaqController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('faqs')->controller(FaqController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('faqs.change_status');
    });

    Route::resources([
        'faqs' => FaqController::class
    ]);
});
