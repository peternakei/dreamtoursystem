<?php

use App\Project\Modules\System\Pages\PageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('pages')->controller(PageController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('pages.change_status');
    });

    Route::resources([
        'pages' => PageController::class
    ]);
});
