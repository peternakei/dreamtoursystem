<?php

namespace App\Project\Workspace;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceResponse
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('X-Safari-Workspace') === '1' && ! $request->isMethod('GET')) {
            $user = $request->user();
            abort_unless($user && $user->profile === 'SystemUser' && $user->is_active && $user->hasRole('SuperAdmin'), 403);
        }
        $response = $next($request);
        if ($request->header('X-Safari-Workspace') === '1' && $response instanceof RedirectResponse) {
            $errors = $request->session()->get('errors');
            if ($errors && $errors->any()) {
                return response()->json(['message' => 'Please check the form.', 'errors' => $errors->getBag('default')->getMessages()], 422);
            }
            $error = $request->session()->get('error');

            return response()->json(['status' => ! $error, 'message' => $error ?: $request->session()->get('success', 'Saved successfully.')], $error ? 422 : 200);
        }

        return $response;
    }
}
