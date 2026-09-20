<?php

namespace App\Project\Modules\Core\Roles\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'SuperAdmin',
            'Admin',
            'SystemUser',
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'uuid' => uniqid(),
                'created_by' => 1,
            ]);
        }
    }
}
