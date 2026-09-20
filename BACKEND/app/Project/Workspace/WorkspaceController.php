<?php

namespace App\Project\Workspace;

use App\Http\Controllers\Controller;
use App\Project\Workspace\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkspaceController extends Controller
{
    public function session(Request $request)
    {
        return response()->json(['user' => $this->profile($request->user()), 'csrf_token' => csrf_token()]);
    }

    private function profile($user): ?array
    {
        if (! $user) {
            return null;
        }

        return ['id' => $user->id, 'name' => $user->userProfile?->name ?? $user->username, 'username' => $user->username, 'roles' => $user->getRoleNames(), 'must_change_password' => (bool) $user->must_change_password];
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['username' => 'required|string', 'password' => 'required|string']);
        $credentials['is_active'] = true;
        $credentials['profile'] = 'SystemUser';
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['username' => ['Incorrect username or password.']]);
        }
        if (! Auth::user()->hasRole('SuperAdmin')) {
            Auth::logout();
            abort(403, 'The administration workspace requires the SuperAdmin role.');
        }
        $request->session()->regenerate();

        return response()->json(['user' => $this->profile(Auth::user()), 'csrf_token' => csrf_token()]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => true]);
    }

    public function modules()
    {
        return response()->json(array_values(PageService::modules()));
    }

    public function index(string $module)
    {
        return response()->json(PageService::page($module));
    }

    public function show(string $module, string $id)
    {
        return response()->json(PageService::page($module, $id));
    }

    public function dashboard()
    {
        $counts = [];
        foreach (['inquiries', 'quotations', 'bookings', 'trips', 'tourists', 'destinations'] as $table) {
            $counts[$table] = DB::table($table)->whereNull('deleted_at')->count();
        }

        return response()->json(['counts' => $counts]);
    }
}
