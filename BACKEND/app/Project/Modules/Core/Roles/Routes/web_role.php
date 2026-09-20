<?php

use App\Project\Modules\Core\Roles\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::resources([
        'roles' => RoleController::class
    ]);

    Route::controller(RoleController::class)->group(function () {
        Route::get('roles/active', 'active')->name('roles.active');
        Route::get('roles/inactive', 'inactive')->name('roles.inactive');
    });
});
