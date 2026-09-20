<?php

namespace App\Project\Modules\Core\Roles\Services;

use App\Project\Modules\Core\Roles\RoleHasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SaveUpdateRoleDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $role = Role::where('uuid', $id)->first();

        $update = $role->update([
            'name' => htmlspecialchars($request->name),
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update role'];
        }

        //assign permissions
        foreach ($request->permission as $permission) {

            $exists = RoleHasPermission::where(['role_id' => $role->id, 'permission_id' => $permission])->get();
            if (count($exists) <= 0) {
                //save role permissions
                $syncPermissions = DB::table('role_has_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission
                ]);

                if (!$syncPermissions) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Failed to assign role permission'];
                }
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Role created successfully.'];
    }
}
