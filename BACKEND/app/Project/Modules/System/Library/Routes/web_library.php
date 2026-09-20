<?php

use App\Project\Modules\System\Library\LibraryDashboardController;
use App\Project\Modules\System\Library\LibraryMediaController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('library', [LibraryDashboardController::class, 'index'])->name('library.index');

    Route::prefix('library')->controller(LibraryMediaController::class)->group(function () {
        Route::post('{type}/{uuid}/media', 'store')->name('library.media.store');
        Route::post('{type}/{uuid}/media/reorder', 'reorder')->name('library.media.reorder');
        Route::patch('{type}/{uuid}/description', 'updateDescription')->name('library.description.update');
        Route::patch('media/{attachment}', 'update')->name('library.media.update');
        Route::delete('media/{attachment}', 'destroy')->name('library.media.destroy');
    });
});
