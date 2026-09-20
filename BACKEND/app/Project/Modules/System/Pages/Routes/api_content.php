<?php

use App\Project\Modules\System\Pages\PageContentController;
use Illuminate\Support\Facades\Route;

Route::get('content-pages/{name}', [PageContentController::class, 'show'])->name('content-pages.show');
Route::get('service-preferences', [PageContentController::class, 'preferences'])->name('service-preferences');
Route::get('travel-content/{type}/{uuid}', [PageContentController::class, 'travel'])->whereUuid('uuid')->name('travel-content.show');
