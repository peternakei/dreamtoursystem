<?php

use App\Project\Modules\Core\Permissions\PermissionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::resources([
        'permissions' => PermissionController::class
    ]);

    Route::controller(PermissionController::class)->group(function () {
        Route::get('permissions/active', 'active')->name('permissions.active');
        Route::get('permissions/inactive', 'inactive')->name('permissions.inactive');
    });
});
