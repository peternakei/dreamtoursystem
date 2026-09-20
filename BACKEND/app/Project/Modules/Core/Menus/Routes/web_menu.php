<?php

use App\Project\Modules\Core\Menus\MenuController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::resources([
        'menus' => MenuController::class
    ]);

    Route::controller(MenuController::class)->group(function () {
        Route::get('menus/active', 'active')->name('menus.active');
        Route::get('menus/inactive', 'inactive')->name('menus.inactive');
    });
});
