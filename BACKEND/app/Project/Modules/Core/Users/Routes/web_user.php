<?php

use App\Project\Modules\Core\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('active', 'active')->name('users.active');
        Route::get('inactive', 'inactive')->name('users.inactive');
        Route::post('reset/{id}', 'resetPassword')->name('users.reset');
        Route::post('change_status/{id}', 'changeStatus')->name('users.change_status');
        Route::post('assign_role/{id}', 'assignRole')->name('users.assign_role');
        Route::post('assign_property/{id}', 'assignProperty')->name('users.assign_property');
        Route::post('revoke_role/{id}', 'revokeRole')->name('users.revoke_role');
        Route::post('update_password/{id}', 'changePassword')->name('users.update_password');
        Route::post('change_property/{id}', 'changeProperty')->name('users.change_property');
        Route::get('profile/{id}', 'profile')->name('users.profile');
    });

    Route::resources([
        'users' => UserController::class
    ]);
});
