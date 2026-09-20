<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RevokeUserRoleFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $user = SystemUser::where('uuid', $id)->first();
        $login = User::where(['profile' => 'SystemUser', 'profile_id' => $user->id])->first();
        $role = Role::find($request->role_id);

        $revoke = $login->removeRole($role->name);
        if (!$revoke) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to revoke user role'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User role revoked successfully'];
    }
}
