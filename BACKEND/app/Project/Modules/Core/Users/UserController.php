<?php

namespace App\Project\Modules\Core\Users;

use App\Project\Modules\Core\Users\Services\AssignUserPropertyFormAction;
use App\Project\Modules\Core\Users\Services\AssignUserRoleFormAction;
use App\Project\Modules\Core\Users\Services\ChangeCurrentPropertyFormAction;
use App\Project\Modules\Core\Users\Services\ChangeUserPasswordFormAction;
use App\Project\Modules\Core\Users\Services\ChangeUserStatusFormAction;
use App\Project\Modules\Core\Users\Services\ResetUserPasswordFormAction;
use App\Project\Modules\Core\Users\Services\RevokeUserRoleFormAction;
use App\Project\Modules\Core\Users\Services\SaveNewUserFormAction;
use App\Project\Modules\Core\Users\Services\SaveUpdateUserFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Users\Requests\AssignUserPropertyFormRequest;
use App\Project\Modules\Core\Users\Requests\AssignUserRoleFormRequest;
use App\Project\Modules\Core\Users\Requests\ChangeCurrentPropertyFormRequest;
use App\Project\Modules\Core\Users\Requests\ChangeUserPasswordFormRequest;
use App\Project\Modules\Core\Users\Requests\ChangeUserStatusFormRequest;
use App\Project\Modules\Core\Users\Requests\CreateNewUserFormRequest;
use App\Project\Modules\Core\Users\Requests\EditUserFormRequest;
use App\Project\Modules\Core\Users\Requests\RevokeUserRoleFormRequest;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use App\Models\Web\System\Property\Property;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get users
        $users = SystemUser::users();
        $roles = $this->visibleRoles();

        return view('web.core.user.index', [
            'title' => 'Users',
            'sub_title' => 'All Users',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function active()
    {
        //get users
        $users = SystemUser::active();
        $roles = $this->visibleRoles();

        return view('web.core.user.index', [
            'title' => 'Users',
            'sub_title' => 'Active Users',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function inactive()
    {
        //get users
        $users = SystemUser::inactive();
        $roles = $this->visibleRoles();

        return view('web.core.user.index', [
            'title' => 'Users',
            'sub_title' => 'Inactive Users',
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewUserFormRequest $request, SaveNewUserFormAction $saveNewUserFormAction)
    {
        //save
        $save = $saveNewUserFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'users'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = SystemUser::where('uuid', $id)->first();
        $this->abortIfHiddenSuperAdmin($user);
        $roles = $this->visibleRoles();

        $statuses = [
            [
                'id' => 1,
                'name' => 'Active'
            ],
            [
                'id' => 0,
                'name' => 'Inactive'
            ]
        ];

        return view('web.core.user.show', ['user' => $user, 'roles' => $roles, 'statuses' => $statuses]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = SystemUser::where('uuid', $id)->first();
        $this->abortIfHiddenSuperAdmin($user);

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $user,
            'html' => '<input type="hidden" name="user_id" id="user_id" value="' . $user->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditUserFormRequest $request, SaveUpdateUserFormAction $saveUpdateUserFormAction, string $id)
    {
        $this->abortIfHiddenSuperAdmin(SystemUser::where('uuid', $id)->first());

        $update = $saveUpdateUserFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'users'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = SystemUser::where('uuid', $id)->first();
        $this->abortIfHiddenSuperAdmin($user);
        User::where(['profile' => 'SystemUser', 'profile_id' => $user->id])->update(['is_active' => false]);

        $delete = $user->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete user details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'User deleted successfully',
            'redirect' => 'users'
        ]);
    }

    public function resetPassword(ResetUserPasswordFormAction $resetUserPasswordFormAction, string $id)
    {
        $this->abortIfHiddenSuperAdmin(SystemUser::where('uuid', $id)->first());

        $reset = $resetUserPasswordFormAction->handle($id);
        if (!$reset) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to reset user details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'User resetted successfully',
            'redirect' => 'users/' . $id
        ]);
    }

    public function changeStatus(ChangeUserStatusFormRequest $request, ChangeUserStatusFormAction $changeUserStatusFormAction, string $id)
    {
        $this->abortIfHiddenSuperAdmin(SystemUser::where('uuid', $id)->first());

        $change = $changeUserStatusFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $change['message'],
            'redirect' => 'users/' . $id
        ]);
    }

    public function assignRole(AssignUserRoleFormRequest $request, AssignUserRoleFormAction $assignUserRoleFormAction, string $id)
    {
        $this->abortIfHiddenSuperAdmin(SystemUser::where('uuid', $id)->first());

        $assign = $assignUserRoleFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'users/' . $id
        ]);
    }



    public function revokeRole(RevokeUserRoleFormRequest $request, RevokeUserRoleFormAction $revokeUserRoleFormAction, string $id)
    {
        $this->abortIfHiddenSuperAdmin(SystemUser::where('uuid', $id)->first());

        $revoke = $revokeUserRoleFormAction->handle($request, $id);
        if (!$revoke['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $revoke['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $revoke['message'],
            'redirect' => 'users/' . $id
        ]);
    }

    public function profile(string $id)
    {
        $login = User::where('id', Auth::user()->id)->first();
        $user = SystemUser::where('id', $login->profile_id)->first();

        return view('web.core.user.profile', ['user' => $user]);
    }

    public function changePassword(ChangeUserPasswordFormRequest $request, ChangeUserPasswordFormAction $changeUserPasswordFormAction, string $id)
    {
        $change = $changeUserPasswordFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message']
            ]);
        }

        User::where('id', Auth::user()->id)->update(['status' => 'LOGGED_OUT']);
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Password changed successfully. Please login with your new password.',
            'redirect' => 'login'
        ]);
    }

    public function changeProperty(ChangeCurrentPropertyFormRequest $request, ChangeCurrentPropertyFormAction $changeCurrentPropertyFormAction, string $id)
    {
        $change = $changeCurrentPropertyFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message'],
                'redirect' => 'users/profile/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $change['message'],
            'redirect' => 'users/profile/' . $id
        ]);
    }

    private function visibleRoles()
    {
        $viewer = Auth::user();

        return Role::query()
            ->when($viewer && ! $viewer->hasRole('SuperAdmin'), function ($query) {
                $query->where('name', '!=', 'SuperAdmin');
            })
            ->get();
    }

    private function abortIfHiddenSuperAdmin(?SystemUser $user): void
    {
        abort_if(! $user, 404);

        $viewer = Auth::user();

        if ($viewer && ! $viewer->hasRole('SuperAdmin') && $user->login?->hasRole('SuperAdmin')) {
            abort(404);
        }
    }
}
