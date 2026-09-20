<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AssignUserRoleFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $user = SystemUser::where('uuid', $id)->first();

        $logins = User::where(['profile' => 'SystemUser', 'profile_id' => $user->id])->first();
        $role = Role::find(filter_var($request->role, FILTER_VALIDATE_INT));
        if ($role->name === 'SuperAdmin' && ! Auth::user()->hasRole('SuperAdmin')) {
            DB::rollBack();
            return ['status' => false, 'message' => 'You are not allowed to assign the SuperAdmin role'];
        }

        $assign = $logins->assignRole($role->name);
        if (!$assign) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to assign user role'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User role assigned successfully'];
    }
}
