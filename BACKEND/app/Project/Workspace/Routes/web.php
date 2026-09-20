<?php

use App\Project\Workspace\WorkspaceAccess;
use App\Project\Workspace\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('workspace')->name('workspace.')->controller(WorkspaceController::class)->group(function () {
    Route::get('session', [WorkspaceController::class, 'session'])->name('session');
    Route::post('login', 'login')->middleware('throttle:6,1')->name('login');
    Route::post('logout', 'logout')->middleware('auth:web')->name('logout');
    Route::middleware(['auth:web', WorkspaceAccess::class])->group(function () {
        Route::get('modules', 'modules')->name('modules');
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('modules/{module}', 'index')->name('index');
        Route::get('modules/{module}/{id}', 'show')->name('show');
    });
});

Route::prefix('workspace/logs')->name('workspace.logs.')->middleware(['auth:web', WorkspaceAccess::class])
    ->controller(\App\Project\Modules\Core\Logs\LogsController::class)->group(function () {
        Route::get('{type}', 'index')->name('index');
        Route::get('{type}/{uuid}', 'show')->whereUuid('uuid')->name('show');
    });

Route::prefix('workspace')->name('workspace.quotes.')->middleware(['auth:web', WorkspaceAccess::class])
    ->controller(\App\Project\Workspace\WorkspaceQuotationController::class)->group(function () {
        Route::get('quotations/{id}', 'show')->whereUuid('id')->name('show');
        Route::post('quotations/{id}/duplicate', 'duplicate')->whereUuid('id')->name('duplicate');
        Route::get('quotation-versions/{id}', 'builder')->whereUuid('id')->name('builder');
        Route::put('quotation-versions/{id}', 'update')->whereUuid('id')->name('update');
        Route::get('quotation-versions/{id}/preview', 'preview')->whereUuid('id')->name('preview');
        Route::post('quotation-versions/{id}/share', 'share')->whereUuid('id')->name('share');
        Route::post('quotation-versions/{id}/pdf', 'pdf')->whereUuid('id')->name('pdf');
        Route::get('inquiries/{id}/quotation-options', 'createOptions')->whereUuid('id')->name('create-options');
        Route::post('inquiries/{id}/quotations', 'create')->whereUuid('id')->name('create');
    });
