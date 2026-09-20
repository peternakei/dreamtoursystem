<?php

namespace App\Project\Modules\Core\Permissions\Seeders;

use App\Project\Modules\Core\Menus\Menu;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Idempotent — safe to re-run.
 *
 * Adds permissions for the new Library/Configuration menus and links them to
 * the corresponding menu rows via permission_menus. Also re-syncs the
 * super-admin role so the permissions are immediately usable.
 */
class LibraryMenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            ['name' => 'view-content-library', 'menu' => 'content_library'],
            ['name' => 'view-library-hub', 'menu' => 'library_hub'],
            ['name' => 'view-configuration', 'menu' => 'configuration'],
            ['name' => 'view-accommodations', 'menu' => 'accommodations'],
            ['name' => 'view-vehicles', 'menu' => 'vehicles'],
        ];

        foreach ($entries as $entry) {
            $menu = Menu::where('name', $entry['menu'])->first();
            if (!$menu) {
                continue;
            }

            $permission = Permission::firstOrCreate(
                ['name' => $entry['name']],
                [
                    'guard_name' => 'web',
                    'created_by' => 1,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'uuid' => (string) Str::orderedUuid(),
                ]
            );

            $exists = DB::table('permission_menus')
                ->where('permission_id', $permission->id)
                ->where('menu_id', $menu->id)
                ->exists();

            if (!$exists) {
                DB::table('permission_menus')->insert([
                    'permission_id' => $permission->id,
                    'menu_id' => $menu->id,
                    'created_by' => 1,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'uuid' => (string) Str::orderedUuid(),
                ]);
            }
        }

        // Super-admin (role 1) and admin (role 2): keep in sync so the new
        // Library/Configuration menus are immediately usable. Mirrors the
        // exclusion list used by RoleAssignPermissionSeeder for role 2.
        $excludedForAdmin = [43, 32, 20, 49, 58, 59, 62];

        if ($superAdmin = Role::find(1)) {
            $superAdmin->syncPermissions(Permission::all());
        }

        if ($admin = Role::find(2)) {
            $admin->syncPermissions(
                Permission::whereNotIn('id', $excludedForAdmin)->get()
            );
        }
    }
}
