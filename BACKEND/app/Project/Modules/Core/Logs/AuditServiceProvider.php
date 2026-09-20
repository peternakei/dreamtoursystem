<?php

namespace App\Project\Modules\Core\Logs;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach (['created', 'updated', 'deleted', 'restored'] as $event) {
            Event::listen('eloquent.'.$event.': *', fn ($name, $models) => AuditLogger::model($event, $models[0]));
        }
        foreach ([Login::class => 'login', Logout::class => 'logout', Failed::class => 'login_failed'] as $event => $action) {
            Event::listen($event, function ($event) use ($action) {
                if ($action !== 'login_failed' && $event->user) request()->attributes->set('audit_user_id', $event->user->getAuthIdentifier());
                AuditLogger::write('activity_logs', array_merge(AuditLogger::context(), [
                    'module' => 'Authentication', 'record_id' => isset($event->user) ? (string) $event->user->getAuthIdentifier() : null,
                    'action' => $action, 'type' => 'SYS_LOG', 'message' => str_replace('_', ' ', $action),
                ]));
            });
        }
    }
}
