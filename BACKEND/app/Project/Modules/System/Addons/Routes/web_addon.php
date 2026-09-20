<?php

use App\Project\Modules\System\Addons\AddonController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('addons')->controller(AddonController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('addons.change_status');
    });

    Route::resources([
        'addons' => AddonController::class
    ]);
});
