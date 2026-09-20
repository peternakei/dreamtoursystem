<?php

namespace App\Project\Modules\Core\Permissions\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NormalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create-menus',
            'edit-menus',
            // 'delete-menus',
            'create-permissions',
            'edit-permissions',
            // 'delete-permissions',
            'create-roles',
            'edit-roles',
            // 'delete-roles',
            'create-exchange-rates',
            'create-bank-details',
            'edit-bank-details',
            // 'delete-bank-details',
            'create-banks',
            'edit-banks',
            // 'delete-banks',
            'create-users',
            'edit-users',
            // 'delete-users',
            'assign-user-roles',
            'assign-user-properties',
            'reset-user-passwords',
            'change-user-statuses',
            'create-receipts',
            // 'delete-invoices',
            'create-receipts',
            'edit-receipts',
            // 'delete-receipts',
            'create-seasons',
            'edit-seasons',
            // 'delete-seasons',
            'create-countries',
            'edit-countries',
            // 'delete-countries',
            'create-locations',
            'edit-locations',
            // 'delete-locations',
            'create-activities',
            'edit-activities',
            // 'delete-activities',
            'create-categories',
            'edit-categories',
            // 'delete-categories',
            'create-addons',
            'edit-addons',
            // 'delete-addons',
            'create-budgets',
            'edit-budgets',
            // 'delete-budgets',
            'create-activity-prices',
            'create-tourists',
            'edit-tourists',
            // 'delete-tourists',
            'create-destinations',
            'edit-destinations',
            // 'delete-destinations',
            'change-destination-status',
            'upload-destination-images',
            'assign-destination-activities',
            'create-destination-facts',
            'assign-destination-categories',
            'create-trips',
            'edit-trips',
            // 'delete-trips',
            'add-trip-addons',
            'assign-trip-category',
            'assign-trip-category-activities',
            'assign-trip-destination',
            'add-trip-group',
            'add-trip-group-camps',
            'add-trip-points',
            'add-trip-price',
            'change-trip-status',
            'create-bookings',
            'edit-bookings',
            // 'delete-bookings',
            'create-invoices',
            'edit-invoices',
            // 'delete-invoices',
            'create-receipts',
            'edit-receipts',
            // 'delete-receipts',
            'create-faqs',
            'edit-faqs',
            // 'delete-faqs',
            'change-faq-status',
            'create-inquiries',
            'edit-inquiries',
            // 'delete-inquiries',
            'change-inquiries-status',
        ];

        foreach ($permissions as $permission) {

            $permissionExists = Permission::where('name', $permission)->first();
            if (!$permissionExists) {

                Permission::create([
                    'name' => $permission,
                    'created_by' => 1,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'uuid' => Str::orderedUuid()
                ]);
            }
        }
    }
}
