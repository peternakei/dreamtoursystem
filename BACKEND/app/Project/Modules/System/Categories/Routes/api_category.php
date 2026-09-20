<?php

use App\Project\Modules\System\Categories\ApiControllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::controller(CategoryController::class)->group(function () {
    Route::get('categories', 'getCategories')->name('categories.index');
});
