<?php

namespace App\Project\Workspace;

use App\Project\Workspace\Services\PageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceLanding
{
    public function handle(Request $request, Closure $next)
    {
        // The local browser entry point is Vue. API/JSON requests and the
        // retained quotation/password tools keep their existing behavior.
        if (app()->environment('local') && in_array($request->method(), ['GET', 'HEAD'], true) && ! $request->expectsJson()) {
            $target = match ($request->path()) {
                '/', 'dashboard', 'dashboard/user' => '/dashboard',
                'login' => '/login',
                default => null,
            };
            if (preg_match('#^quotations/([a-f0-9-]{36})$#i', $request->path(), $matches)) {
                $target = '/quotations/'.$matches[1].'/details';
            } elseif (preg_match('#^quotation-versions/([a-f0-9-]{36})/(builder|preview)$#i', $request->path(), $matches)) {
                $target = '/quotation-versions/'.$matches[1].'/'.$matches[2];
            } elseif (preg_match('#^inquiries/([a-f0-9-]{36})/quotations/create$#i', $request->path(), $matches)) {
                $target = '/inquiries/'.$matches[1].'/quotations/new';
            }
            if (preg_match('#^(bookings|tourists|invoices|receipts)(?:/([a-f0-9-]{36}))?$#i', $request->path(), $matches)) {
                $target = '/'.$matches[1].(isset($matches[2]) ? '/'.$matches[2].'/details' : '/list');
            }
            $parts = explode('/', $request->path());
            if (isset(PageService::modules()[$parts[0]])) {
                if (count($parts) === 1) {
                    $target = '/'.$parts[0].'/list';
                }
                if (isset($parts[1]) && (Str::isUuid($parts[1]) || preg_match('/^[a-f0-9]{13}$/i', $parts[1])) && (count($parts) === 2 || (count($parts) === 3 && $parts[2] === 'edit'))) {
                    $target = '/'.$parts[0].'/'.$parts[1].'/details';
                }
                if ($parts[0] === 'trips' && isset($parts[1]) && Str::isUuid($parts[1]) && ($parts[2] ?? '') === 'planner') {
                    $target = '/trips/'.$parts[1].'/details?tab=itinerary';
                }
            }
            if ($request->path() === 'library') {
                $target = '/accommodations/list';
            }
            if ($target !== null) {
                return redirect()->away(rtrim(config('workspace.frontend_url'), '/').$target);
            }
        }

        return $next($request);
    }
}
