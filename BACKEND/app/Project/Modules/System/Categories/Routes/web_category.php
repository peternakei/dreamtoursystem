<?php

use App\Project\Modules\System\Categories\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('categories.index');
        Route::post('/', 'store')->name('categories.store');
        Route::get('{category}', 'show')->name('categories.show');
        Route::put('{category}', 'update')->name('categories.update');
        Route::post('change_status/{category}', 'changeStatus')->name('categories.change_status');
        Route::delete('{category}', 'destroy')->name('categories.destroy');
    });
});
