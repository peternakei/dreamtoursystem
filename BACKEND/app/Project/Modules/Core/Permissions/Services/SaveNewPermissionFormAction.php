<?php

namespace App\Project\Modules\Core\Permissions\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Project\Modules\Core\Menus\PermissionMenu;

class SaveNewPermissionFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $save = Permission::create([
            'name' => htmlspecialchars($request->name),
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save permission details'];
        }

        //check if is permission menu
        if (isset($request->menu)) {
            //save menu
            $permissionMenu = PermissionMenu::create([
                'permission_id' => $save->id,
                'menu_id' => $request->menu,
                'created_by' => Auth::user()->id,
            ]);

            if (!$permissionMenu) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to save menu permission details'];
            }
        }

        //assign roles
        foreach ($request->role as $role) {

            //save permission roles
            $syncRoles = DB::table('role_has_permissions')->insert([
                'role_id' => $role,
                'permission_id' => $save->id
            ]);

            if (!$syncRoles) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to assign role permission'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Permission created successfully.'];
    }
}
