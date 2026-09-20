<?php

use App\Project\Modules\System\Budgets\BudgetController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('budgets')->controller(BudgetController::class)->group(function () {
        Route::post('change_status/{id}', 'changeStatus')->name('budgets.change_status');
    });

    Route::resources([
        'budgets' => BudgetController::class
    ]);
});
