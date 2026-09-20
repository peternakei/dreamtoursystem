<?php

namespace App\Project\Modules\Core\Permissions\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class SaveUpdatePermissionDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $permission = Permission::where('uuid', $id)->first();

        $update = $permission->update([
            'name' => $request->name,
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to assign role permission'];
        }

        //assign roles
        foreach ($request->role as $role) {

            $exists = DB::table('role_has_permissions')->where(['role_id' => $role, 'permission_id' => $permission->id])->get();
            if (count($exists) <= 0) {

                //save permission roles
                $syncRoles = DB::table('role_has_permissions')->insert([
                    'role_id' => $role,
                    'permission_id' => $permission->id
                ]);

                if (!$syncRoles) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Failed to assign role permission'];
                }
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Permission created successfully.'];
    }
}
