<?php

namespace App\Project\Modules\Core\Users\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Project\Modules\Core\Users\User;
use Spatie\Permission\Models\Permission;

class AssignUserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()
            ->where('username', 'admin@serenbluesafaris.com')
            ->firstOrFail();

        $user->assignRole('SuperAdmin');
        $user->syncPermissions(Permission::all());
    }
}
