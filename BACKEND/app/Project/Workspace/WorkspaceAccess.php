<?php

namespace App\Project\Workspace;

use Closure;
use Illuminate\Http\Request;

class WorkspaceAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        abort_unless($user && $user->profile === 'SystemUser' && $user->is_active && $user->hasRole('SuperAdmin'), 403, 'The administration workspace requires the SuperAdmin role.');

        return $next($request);
    }
}
