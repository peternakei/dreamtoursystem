<?php

use App\Project\Modules\System\Reports\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('reports')->controller(ReportController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('reports.change_status');
    });

    Route::resources([
        'reports' => ReportController::class
    ]);
});
