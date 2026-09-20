<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->must_change_password || $this->isAllowedRoute($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'code' => 423,
                'message' => 'Please change your password to continue.',
                'redirect' => 'force-password-change',
            ], 423);
        }

        return redirect()->route('password.force');
    }

    private function isAllowedRoute(Request $request): bool
    {
        return $request->routeIs(
            'workspace.session',
            'workspace.login',
            'workspace.logout',
            'password.force',
            'password.force.update',
            'logout',
            'login',
            'authenticate-user'
        );
    }
}
