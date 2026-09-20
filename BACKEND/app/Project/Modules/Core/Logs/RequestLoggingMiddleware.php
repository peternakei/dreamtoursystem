<?php

namespace App\Project\Modules\Core\Logs;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestLoggingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('audit_request_id', (string) Str::uuid());
        $started = microtime(true);
        $response = $next($request);
        if (!$request->isMethod('OPTIONS') && !$request->is('up')) {
            AuditLogger::write('request_logs', array_merge(AuditLogger::context(), [
                'method' => $request->method(),
                // The route template avoids storing secrets embedded in URLs or query strings.
                'url' => '/'.($request->route()?->uri() ?? '[unmatched]'),
                'route_name' => $request->route()?->getName(), 'ip_address' => $request->ip(),
                'response_status' => $response->getStatusCode(),
                'duration' => max(0, (int) round((microtime(true) - $started) * 1000)),
                'payload' => ['fields' => array_slice(array_keys($request->except(['password', '_token', 'token'])), 0, 100)],
            ]));
        }
        // Query-builder and pivot writes do not emit Eloquent model events. Their HTTP
        // outcomes remain visible here, without claiming field-level change history.
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $status = $response->getStatusCode();
            AuditLogger::write('activity_logs', array_merge(AuditLogger::context(), [
                'module' => 'HTTP',
                'action' => $status >= 500 ? 'request_failed' : ($status >= 400 ? 'request_rejected' : 'request_finished'),
                'type' => 'INFO', 'message' => $request->method().' /'.($request->route()?->uri() ?? '[unmatched]'),
                'new_data' => ['response_status' => $status],
            ]));
        }
        $response->headers->set('X-Request-ID', $request->attributes->get('audit_request_id'));
        return $response;
    }
}
