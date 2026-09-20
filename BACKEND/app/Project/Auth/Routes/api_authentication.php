<?php

use App\Project\Auth\ApiControllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('reset-password', 'resetPassword')->name('reset-password');
    Route::post('change_password', 'changePassword')->name('change_password')->middleware('auth:sanctum');
});
