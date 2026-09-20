<?php

use App\Project\Modules\System\Blogs\BlogController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('blogs')->controller(BlogController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('blogs.change_status');
    });

    Route::resources([
        'blogs' => BlogController::class
    ]);
});
