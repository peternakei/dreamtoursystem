<?php

use App\Project\Workspace\WorkspaceAccess;
use App\Project\Workspace\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('workspace')->name('workspace.')->controller(WorkspaceController::class)->group(function () {
    Route::get('session', 'session')->name('session');
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
