<?php

use App\Project\Modules\Core\SystemConfigurations\SystemConfigurationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::resources([
        'system_configurations' => SystemConfigurationController::class
    ]);
});
