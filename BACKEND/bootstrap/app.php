<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsurePasswordIsChanged;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(\App\Project\Modules\Core\Logs\RequestLoggingMiddleware::class);
        $middleware->alias([
            'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
        ]);

        $middleware->appendToGroup('web', [
            EnsurePasswordIsChanged::class,
            \App\Project\Workspace\WorkspaceResponse::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/payment-mobile',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $exception) {
            \App\Project\Modules\Core\Logs\AuditLogger::error($exception);
        });
    })->create();
