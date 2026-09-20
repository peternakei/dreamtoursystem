<?php

namespace App\Project\Modules\Core\Permissions\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Project\Modules\Core\Menus\Menu;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'view-tourists',
                'menu' => 'tourists'
            ],
            [
                'name' => 'view-all-tourists',
                'menu' => 'all_tourists'
            ],
            [
                'name' => 'view-active-tourists',
                'menu' => 'active_tourists'
            ],
            [
                'name' => 'view-inactive-tourists',
                'menu' => 'inactive_tourists'
            ],
            [
                'name' => 'view-destinations',
                'menu' => 'destinations'
            ],
            [
                'name' => 'view-all-destinations',
                'menu' => 'all_destinations'
            ],
            [
                'name' => 'view-active-destinations',
                'menu' => 'active_destinations'
            ],
            [
                'name' => 'view-inactive-destinations',
                'menu' => 'inactive_destinations'
            ],
            [
                'name' => 'view-trips',
                'menu' => 'trips'
            ],
            [
                'name' => 'view-all-trips',
                'menu' => 'all_trips'
            ],
            [
                'name' => 'view-approved-trips',
                'menu' => 'approved_trips'
            ],
            [
                'name' => 'view-pending-trips',
                'menu' => 'pending_trips'
            ],
            [
                'name' => 'view-cancelled-trips',
                'menu' => 'cancelled_trips'
            ],
            [
                'name' => 'view-completed-trips',
                'menu' => 'completed_trips'
            ],
            [
                'name' => 'view-requests',
                'menu' => 'requests'
            ],
            [
                'name' => 'view-inquiries',
                'menu' => 'inquiries'
            ],
            [
                'name' => 'view-all-inquiries',
                'menu' => 'all_inquiries'
            ],
            [
                'name' => 'view-processed-inquiries',
                'menu' => 'processed_inquiries'
            ],
            [
                'name' => 'view-pending-inquiries',
                'menu' => 'pending_inquiries'
            ],
            [
                'name' => 'view-quotations',
                'menu' => 'quotations'
            ],
            [
                'name' => 'view-all-quotations',
                'menu' => 'all_quotations'
            ],
            [
                'name' => 'view-won-quotations',
                'menu' => 'won_quotations'
            ],
            [
                'name' => 'view-open-quotations',
                'menu' => 'open_quotations'
            ],
            [
                'name' => 'view-lost-quotations',
                'menu' => 'lost_quotations'
            ],
            [
                'name' => 'view-bookings',
                'menu' => 'bookings'
            ],
            [
                'name' => 'view-all-bookings',
                'menu' => 'all_bookings'
            ],
            [
                'name' => 'view-completed-bookings',
                'menu' => 'completed_bookings'
            ],
            [
                'name' => 'view-reserved-bookings',
                'menu' => 'reserved_bookings'
            ],
            [
                'name' => 'view-confirmed-bookings',
                'menu' => 'confirmed_bookings'
            ],
            [
                'name' => 'view-cancelled-bookings',
                'menu' => 'cancelled_bookings'
            ],
            [
                'name' => 'view-expired-bookings',
                'menu' => 'expired_bookings'
            ],
            [
                'name' => 'view-payments',
                'menu' => 'payments'
            ],
            [
                'name' => 'view-invoices',
                'menu' => 'invoices'
            ],
            [
                'name' => 'view-invoices',
                'menu' => 'invoices'
            ],
            [
                'name' => 'view-invoices',
                'menu' => 'invoices'
            ],
            [
                'name' => 'view-all-invoices',
                'menu' => 'all_invoices'
            ],
            [
                'name' => 'view-fully-paid-invoices',
                'menu' => 'fully_paid_invoices'
            ],
            [
                'name' => 'view-partial-paid-invoices',
                'menu' => 'partial_paid_invoices'
            ],
            [
                'name' => 'view-pending-invoices',
                'menu' => 'pending_invoices'
            ],
            [
                'name' => 'view-cancelled-invoices',
                'menu' => 'cancelled_invoices'
            ],
            [
                'name' => 'view-expired-invoices',
                'menu' => 'expired_invoices'
            ],
            [
                'name' => 'view-refunded-invoices',
                'menu' => 'refunded_invoices'
            ],
            [
                'name' => 'view-receipts',
                'menu' => 'receipts'
            ],
            [
                'name' => 'view-all-receipts',
                'menu' => 'all_receipts'
            ],
            // [
            //     'name' => 'view-refunds',
            //     'menu' => 'refunds'
            // ],
            // [
            //     'name' => 'view-all-refunds',
            //     'menu' => 'all_refunds'
            // ],
            // [
            //     'name' => 'view-blogs',
            //     'menu' => 'blogs'
            // ],
            // [
            //     'name' => 'view-all-blogs',
            //     'menu' => 'all_blogs'
            // ],
            [
                'name' => 'view-cms',
                'menu' => 'cms'
            ],
            [
                'name' => 'view-user-experiences',
                'menu' => 'user_experiences'
            ],
            [
                'name' => 'view-ratings',
                'menu' => 'ratings'
            ],
            [
                'name' => 'view-all-ratings',
                'menu' => 'all_ratings'
            ],
            [
                'name' => 'view-faqs',
                'menu' => 'faqs'
            ],
            [
                'name' => 'view-all-faqs',
                'menu' => 'all_faqs'
            ],
            // [
            //     'name' => 'view-pages',
            //     'menu' => 'pages'
            // ],
            // [
            //     'name' => 'view-all-pages',
            //     'menu' => 'all_pages'
            // ],
            [
                'name' => 'view-subscriptions',
                'menu' => 'subscriptions'
            ],
            [
                'name' => 'view-all-subscriptions',
                'menu' => 'all_subscriptions'
            ],
            [
                'name' => 'view-testimonials',
                'menu' => 'testimonials'
            ],
            [
                'name' => 'view-all-testimonials',
                'menu' => 'all_testimonials'
            ],
            // [
            //     'name' => 'view-reports',
            //     'menu' => 'reports'
            // ],
            // [
            //     'name' => 'view-all-reports',
            //     'menu' => 'all_reports'
            // ],
            [
                'name' => 'view-users',
                'menu' => 'users'
            ],
            [
                'name' => 'view-all-users',
                'menu' => 'all_users'
            ],
            [
                'name' => 'view-active-users',
                'menu' => 'active_users'
            ],
            [
                'name' => 'view-inactive-users',
                'menu' => 'inactive_users'
            ],
            // [
            //     'name' => 'view-reports',
            //     'menu' => 'reports_users'
            // ],
            // [
            //     'name' => 'view-all-reports',
            //     'menu' => 'all_users'
            // ],
            [
                'name' => 'view-settings',
                'menu' => 'settings'
            ],
            [
                'name' => 'view-banks',
                'menu' => 'banks'
            ],
            [
                'name' => 'view-bank-details',
                'menu' => 'bank_details'
            ],
            [
                'name' => 'view-exchange-rates',
                'menu' => 'exchange_rates'
            ],
            [
                'name' => 'view-seasons',
                'menu' => 'seasons'
            ],
            // [
            //     'name' => 'view-countries',
            //     'menu' => 'countries'
            // ],
            // [
            //     'name' => 'view-regions',
            //     'menu' => 'regions'
            // ],
            // [
            //     'name' => 'view-districts',
            //     'menu' => 'districts'
            // ],
            [
                'name' => 'view-locations',
                'menu' => 'locations'
            ],
            [
                'name' => 'view-activities',
                'menu' => 'activities'
            ],
            [
                'name' => 'view-categories',
                'menu' => 'categories'
            ],
            [
                'name' => 'view-addons',
                'menu' => 'addons'
            ],
            [
                'name' => 'view-budgets',
                'menu' => 'budgets'
            ],
            // [
            //     'name' => 'view-permissions',
            //     'menu' => 'permissions'
            // ],
            // [
            //     'name' => 'view-roles',
            //     'menu' => 'roles'
            // ],
            // [
            //     'name' => 'view-menus',
            //     'menu' => 'menus'
            // ],
        ];

        foreach ($permissions as $permission) {

            //Get the menu details for the permission
            $menu = Menu::where('name', $permission['menu'])->first();

            $permissionExists = Permission::where('name', $permission['name'])->first();
            if (!$permissionExists) {

                $permission = Permission::create([
                    'name' => $permission['name'],
                    'created_by' => 1,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'uuid' => Str::orderedUuid()
                ]);

                //Attach the permission to the menu
                if ($menu) {
                    DB::table('permission_menus')->insert([
                        'permission_id' => $permission->id,
                        'menu_id' => $menu->id,
                        'created_by' => 1,
                        'is_active' => true,
                        'created_at' => Carbon::now(),
                        'uuid' => Str::orderedUuid()
                    ]);
                }
            }
        }
    }
}
