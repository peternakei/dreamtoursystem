<?php

use App\Project\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'web.core.authentication.login');
Route::view('login', 'web.core.authentication.login')->name('login');
Route::view('forgot-password', 'web.core.authentication.forgot_password')->name('forgot.password');

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('forgot_password', 'submitResetPassword')->name('forgot_password');
    Route::get('reset_password/{token}', 'showResetPasswordForm')->name('reset_password');
    Route::post('post_reset_password', 'submitResetPasswordForm')->name('post_reset_password');
    Route::post('login', 'authenticate')->name('authenticate-user');
    Route::get('force-password-change', 'showForcePasswordChangeForm')->name('password.force')->middleware('auth:web');
    Route::post('force-password-change', 'submitForcePasswordChangeForm')->name('password.force.update')->middleware('auth:web');

    Route::post('logout', 'logout')->name('logout')->middleware('auth:web');
});
