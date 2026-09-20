<?php

namespace App\Project\Modules\System\Inquiries;

use Closure;
use Illuminate\Http\Request;

class ServiceAccess
{
    public static function allowed($user, string $permission = 'manage_service_inquiries'): bool
    {
        return $user && $user->profile === 'SystemUser' && $user->is_active && ($user->hasRole('SuperAdmin') || $user->getAllPermissions()->contains(fn ($p) => $p->name === $permission && $p->guard_name === 'web' && $p->is_active));
    }

    public function handle(Request $request, Closure $next, string $permission = 'manage_service_inquiries')
    {
        $user = $request->user();
        abort_unless(self::allowed($user, $permission), 403);

        return $next($request);
    }
}
