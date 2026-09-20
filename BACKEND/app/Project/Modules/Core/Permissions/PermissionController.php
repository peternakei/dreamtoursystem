<?php

namespace App\Project\Modules\Core\Permissions;

use App\Project\Modules\Core\Permissions\Services\SaveNewPermissionFormAction;
use App\Project\Modules\Core\Permissions\Services\SaveUpdatePermissionDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Permissions\Requests\CreateNewPermissionFormRequest;
use App\Project\Modules\Core\Permissions\Requests\EditPermissionDetailsFormRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Project\Modules\Core\Menus\Menu;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get permissions
        $permissions = Permission::orderBy('created_at', 'DESC')->get();
        $roles = Role::all();
        $menus = Menu::all();

        return view('web.core.permission.index', [
            'permissions' => $permissions,
            'roles' => $roles,
            'menus' => $menus,
            'title' => 'Permissions',
            'sub_title' => 'All Permissions'
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
    public function store(CreateNewPermissionFormRequest $request, SaveNewPermissionFormAction $saveNewPermissionFormAction)
    {
        $save = $saveNewPermissionFormAction->handle($request);
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
            'redirect' => 'permissions'
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
        $permission = Permission::where('uuid', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $permission,
            'roles' => Role::all(),
            'selectedRoles' => $permission->roles()->pluck('role_id'),
            'html' => '<input type="hidden" name="permission_id" id="permission_id" value="' . $permission->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditPermissionDetailsFormRequest $request, SaveUpdatePermissionDetailsFormAction $saveUpdatePermissionDetailsFormAction, string $id)
    {
        //update
        $update = $saveUpdatePermissionDetailsFormAction->handle($request, $id);
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
            'redirect' => 'permissions'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::where('uuid', $id)->first();
        DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
        DB::table('permission_menus')->where('permission_id', $permission->id)->delete();
        DB::table('role_has_permissions')->where('permission_id', $permission->id)->delete();
        $deletePermission = DB::table('permissions')->where('id', $permission->id)->delete();

        if (!$deletePermission) {
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
            'redirect' => 'permissions'
        ]);
    }
}
