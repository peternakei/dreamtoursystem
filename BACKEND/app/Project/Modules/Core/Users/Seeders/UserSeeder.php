<?php

namespace App\Project\Modules\Core\Users\Seeders;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // In CLI seeding there is no authenticated user; fall back to the row's
        // own id (self-reference) so the very first user can be its own creator.
        $authId = Auth::id();

        $users = [
            // [
            //     'name' => 'Joshua Mbwambo',
            //     'email' => 'joshua@serenbluesafaris.com',
            //     'phone' => '+255777299690',
            //     'address' => 'Nungwi, Zanzibar',
            //     'password' => bcrypt('1234567890'),
            // ],
            [
                'name' => 'Administrator Emanuel',
                'email' => 'admin@serenbluesafaris.com',
                'phone' => '+255777299630',
                'address' => 'Nungwi, Zanzibar',
                'password' => bcrypt('1234567890'),
            ],
        ];

        DB::transaction(function () use ($users, $authId) {
            foreach ($users as $data) {
                if (User::where('username', $data['email'])->exists()) {
                    continue;
                }
                // users.profile_id is a polymorphic integer, so create the user
                // before the profile without disabling referential integrity.
                $user = User::create([
                    'username' => $data['email'],
                    'password' => $data['password'],
                    'profile' => 'SystemUser',
                    'profile_id' => 0,
                    'created_by' => $authId,
                    'status' => 'LOGGED_OUT',
                ]);
                $ownerId = $authId ?? $user->getKey();
                $profile = SystemUser::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'created_by' => $ownerId,
                ]);
                $user->update(['profile_id' => $profile->getKey(), 'created_by' => $ownerId]);
            }
        });
    }
}
