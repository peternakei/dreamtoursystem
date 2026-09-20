<?php

namespace App\Project\Modules\Core\Roles\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::find(1);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        //admin role
        $role = Role::find(2);
        $permissions = Permission::whereNotIn('id',[43,32,20,49,58,59,62])->get();
        $role->syncPermissions($permissions);
    }
}
