<?php

namespace App\Project\Modules\System\Inquiries\Seeders;

use App\Project\Modules\Core\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DreamServicePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('profile', 'SystemUser')->whereHas('roles', fn ($q) => $q->where('name', 'SuperAdmin'))->first();
        if (! $admin) {
            throw new \RuntimeException('Create the existing SuperAdmin account before seeding service permissions.');
        }
        foreach (['manage_rental_offers', 'manage_service_inquiries', 'manage_page_content'] as $name) {
            $permission = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'], ['uuid' => (string) Str::orderedUuid(), 'created_by' => $admin->id, 'is_active' => true]);
            Role::findByName('SuperAdmin', 'web')->givePermissionTo($permission);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
