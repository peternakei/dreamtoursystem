<?php

namespace App\Project\Modules\Core\Roles;

use App\Project\Modules\Core\Roles\Services\SaveNewRoleFormAction;
use App\Project\Modules\Core\Roles\Services\SaveUpdateRoleDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Roles\Requests\CreateNewRoleFormRequest;
use App\Project\Modules\Core\Roles\Requests\EditRoleDetailsFormRequest;
use App\Project\Modules\Core\Menus\Menu;
use App\Project\Modules\Core\Menus\PermissionMenu;
use App\Project\Modules\Core\Roles\RoleHasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get roles
        $roles = Role::orderBy('created_at', 'DESC')->get();
        $permissions = Permission::all();

        return view('web.core.role.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'title' => 'Roles',
            'sub_title' => 'All Roles'
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
    public function store(CreateNewRoleFormRequest $request, SaveNewRoleFormAction $saveNewRoleFormAction)
    {
        $save = $saveNewRoleFormAction->handle($request);
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
            'redirect' => 'roles'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::where('uuid', '=', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $role,
            'permissions' => Permission::all(),
            'selectedPermissions' => $role->permissions()->pluck('permission_id'),
            'html' => '<input type="hidden" name="role_id" id="role_id" value="' . $role->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditRoleDetailsFormRequest $request, SaveUpdateRoleDetailsFormAction $saveUpdateRoleDetailsFormAction, string $id)
    {
        $update = $saveUpdateRoleDetailsFormAction->handle($request, $id);
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
            'redirect' => 'roles'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::where('uuid', $id)->first();

        RoleHasPermission::where('role_id', $role->id)->delete();
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();

        $delete = Role::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete permission details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Permission deleted successfully',
            'redirect' => 'roles'
        ]);
    }
}
