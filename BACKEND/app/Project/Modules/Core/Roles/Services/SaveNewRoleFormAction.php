<?php

namespace App\Project\Modules\Core\Roles\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SaveNewRoleFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $save = Role::create([
            'name' => htmlspecialchars($request->name),
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save new role'];
        }

        //assign permissions
        foreach ($request->permission as $permission) {

            //save role permissions
            $syncPermissions = DB::table('role_has_permissions')->insert([
                'role_id' => $save->id,
                'permission_id' => $permission
            ]);

            if (!$syncPermissions) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to assign role permission'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Role created successfully.'];
    }
}
