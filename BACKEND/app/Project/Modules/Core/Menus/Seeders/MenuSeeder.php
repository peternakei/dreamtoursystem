<?php

namespace App\Project\Modules\Core\Menus\Seeders;

use App\Project\Modules\Core\Menus\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            'tourists' => [
                'name' => 'tourists',
                'title' => 'Tourists',
                'icon' => 'uil-raddit-alien-alt',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 1,
                'children' => [
                    'all_tourists' => [
                        'name' => 'all_tourists',
                        'title' => 'All',
                        'icon' => '',
                        'url' => 'tourists.index',
                        'menu_id' => 1,
                        'created_by' => 1,
                        'ordering' => 1,
                    ],
                    'active_tourists' => [
                        'name' => 'active_tourists',
                        'title' => 'Active',
                        'icon' => '',
                        'url' => 'tourists.active',
                        'menu_id' => 1,
                        'created_by' => 1,
                        'ordering' => 2,
                    ],
                    'inactive_tourists' => [
                        'name' => 'inactive_tourists',
                        'title' => 'Inactive',
                        'icon' => '',
                        'url' => 'tourists.inactive',
                        'menu_id' => 1,
                        'created_by' => 1,
                        'ordering' => 2,
                    ],
                ]
            ],
            'destinations' => [
                'name' => 'destinations',
                'title' => 'Destinations',
                'icon' => 'uil-webcam',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 2,
                'children' => [
                    'all_destinations' => [
                        'name' => 'all_destinations',
                        'title' => 'All',
                        'icon' => '',
                        'url' => 'destinations.index',
                        'menu_id' => 5,
                        'created_by' => 1,
                        'ordering' => 1,
                    ],
                    'active_destinations' => [
                        'name' => 'active_destinations',
                        'title' => 'Active',
                        'icon' => '',
                        'url' => 'destinations.active',
                        'menu_id' => 5,
                        'created_by' => 1,
                        'ordering' => 2,
                    ],
                    'inactive_destinations' => [
                        'name' => 'inactive_destinations',
                        'title' => 'Inactive',
                        'icon' => '',
                        'url' => 'destinations.inactive',
                        'menu_id' => 5,
                        'created_by' => 1,
                        'ordering' => 3,
                    ],
                ]
            ],
            'trips' => [
                'name' => 'trips',
                'title' => 'Trips',
                'icon' => 'uil-navigator',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 3,
                'children' => [
                    'all_trips' => [
                        'name' => 'all_trips',
                        'title' => 'All',
                        'icon' => '',
                        'url' => 'trips.index',
                        'menu_id' => 9,
                        'created_by' => 1,
                        'ordering' => 1,
                    ],
                    'approved_trips' => [
                        'name' => 'approved_trips',
                        'title' => 'Approved',
                        'icon' => '',
                        'url' => 'trips.approved',
                        'menu_id' => 9,
                        'created_by' => 1,
                        'ordering' => 2,
                    ],
                    'pending_trips' => [
                        'name' => 'pending_trips',
                        'title' => 'Pending',
                        'icon' => '',
                        'url' => 'trips.pending',
                        'menu_id' => 9,
                        'created_by' => 1,
                        'ordering' => 3,
                    ],
                    'cancelled_trips' => [
                        'name' => 'cancelled_trips',
                        'title' => 'Cancelled',
                        'icon' => '',
                        'url' => 'trips.cancelled',
                        'menu_id' => 9,
                        'created_by' => 1,
                        'ordering' => 4,
                    ],
                    'completed_trips' => [
                        'name' => 'completed_trips',
                        'title' => 'Completed',
                        'icon' => '',
                        'url' => 'trips.completed',
                        'menu_id' => 9,
                        'created_by' => 1,
                        'ordering' => 5,
                    ],
                ]
            ],
            'requests' => [
                'name' => 'requests',
                'title' => 'Requests',
                'icon' => 'uil-money-stack',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 4,
                'children' => [
                    'inquiries' => [
                        'name' => 'inquiries',
                        'title' => 'Inquiries',
                        'icon' => ' uil-servers',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 4,
                        'children' => [
                            'all_inquiries' => [
                                'name' => 'all_inquiries',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'inquiries.index',
                                'menu_id' => 15,
                                'created_by' => 1,
                                'ordering' => 1,
                            ],
                            'processed_inquiries' => [
                                'name' => 'processed_inquiries',
                                'title' => 'Processed',
                                'icon' => '',
                                'url' => 'inquiries.processed',
                                'menu_id' => 15,
                                'created_by' => 1,
                                'ordering' => 2,
                            ],
                            'pending_inquiries' => [
                                'name' => 'pending_inquiries',
                                'title' => 'Pending',
                                'icon' => '',
                                'url' => 'inquiries.pending',
                                'menu_id' => 15,
                                'created_by' => 1,
                                'ordering' => 3,
                            ],
                        ]
                    ],
                    'quotations' => [
                        'name' => 'quotations',
                        'title' => 'Quotations',
                        'icon' => ' uil-notes',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 5,
                        'children' => [
                            'all_quotations' => [
                                'name' => 'all_quotations',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'quotations.index',
                                'menu_id' => 19,
                                'created_by' => 1,
                                'ordering' => 1,
                            ],
                            'won_quotations' => [
                                'name' => 'won_quotations',
                                'title' => 'Won',
                                'icon' => '',
                                'url' => 'quotations.won',
                                'menu_id' => 19,
                                'created_by' => 1,
                                'ordering' => 2,
                            ],
                            'open_quotations' => [
                                'name' => 'open_quotations',
                                'title' => 'Open',
                                'icon' => '',
                                'url' => 'quotations.open',
                                'menu_id' => 19,
                                'created_by' => 1,
                                'ordering' => 3,
                            ],
                            'lost_quotations' => [
                                'name' => 'lost_quotations',
                                'title' => 'Lost',
                                'icon' => '',
                                'url' => 'quotations.lost',
                                'menu_id' => 19,
                                'created_by' => 1,
                                'ordering' => 4,
                            ],
                        ]
                    ],
                    'bookings' => [
                        'name' => 'bookings',
                        'title' => 'Bookings',
                        'icon' => 'uil-slack-alt',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 6,
                        'children' => [
                            'all_bookings' => [
                                'name' => 'all_bookings',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'bookings.index',
                                'menu_id' => 24,
                                'created_by' => 1,
                                'ordering' => 1,
                            ],
                            'completed_bookings' => [
                                'name' => 'completed_bookings',
                                'title' => 'Completed',
                                'icon' => '',
                                'url' => 'bookings.completed',
                                'menu_id' => 24,
                                'created_by' => 1,
                                'ordering' => 2,
                            ],
                            'reserved_bookings' => [
                                'name' => 'reserved_bookings',
                                'title' => 'Reserved',
                                'icon' => '',
                                'url' => 'bookings.reserved',
                                'menu_id' => 24,
                                'created_by' => 1,
                                'ordering' => 3,
                            ],
                            'confirmed_bookings' => [
                                'name' => 'confirmed_bookings',
                                'title' => 'Confirmed',
                                'icon' => '',
                                'url' => 'bookings.confirmed',
                                'menu_id' => 24,
                                'created_by' => 1,
                                'ordering' => 4,
                            ],
                            'cancelled_bookings' => [
                                'name' => 'cancelled_bookings',
                                'title' => 'Cancelled',
                                'icon' => '',
                                'url' => 'bookings.cancelled',
                                'menu_id' => 24,
                                'created_by' => 1,
                                'ordering' => 5,
                            ],
                            'expired_bookings' => [
                                'name' => 'expired_bookings',
                                'title' => 'Expired',
                                'icon' => '',
                                'url' => 'bookings.expired',
                                'menu_id' => 24,
                                'created_by' => 1,
                                'ordering' => 6,
                            ],
                        ]
                    ],
                ]
            ],
            'payments' => [
                'name' => 'payments',
                'title' => 'Payments',
                'icon' => 'uil-money-stack',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 5,
                'children' => [
                    'invoices' => [
                        'name' => 'invoices',
                        'title' => 'Invoices',
                        'icon' => 'uil-invoice',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 7,
                        'children' => [
                            'all_invoices' => [
                                'name' => 'all_invoices',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'invoices.index',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 1,
                            ],
                            'fully_paid_invoices' => [
                                'name' => 'fully_paid_invoices',
                                'title' => 'Fully Paid',
                                'icon' => '',
                                'url' => 'invoices.fully_paid',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 2,
                            ],
                            'partial_paid_invoices' => [
                                'name' => 'partial_paid_invoices',
                                'title' => 'Partial Paid',
                                'icon' => '',
                                'url' => 'invoices.partial_paid',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 3,
                            ],
                            'pending_invoices' => [
                                'name' => 'pending_invoices',
                                'title' => 'Pending',
                                'url' => 'invoices.pending',
                                'icon' => '',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 4,
                            ],
                            'cancelled_invoices' => [
                                'name' => 'cancelled_invoices',
                                'title' => 'Cancelled',
                                'url' => 'invoices.cancelled',
                                'icon' => '',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 5,
                            ],
                            'expired_invoices' => [
                                'name' => 'expired_invoices',
                                'title' => 'Expired',
                                'url' => 'invoices.expired',
                                'icon' => '',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 6,
                            ],
                            'refunded_invoices' => [
                                'name' => 'refunded_invoices',
                                'title' => 'Refunded',
                                'url' => 'invoices.refunded',
                                'icon' => '',
                                'menu_id' => 31,
                                'created_by' => 1,
                                'ordering' => 7,
                            ],
                        ]
                    ],
                    'receipts' => [
                        'name' => 'receipts',
                        'title' => 'Receipts',
                        'icon' => 'uil-money-stack',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 8,
                        'children' => [
                            'all_receipts' => [
                                'name' => 'all_receipts',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'receipts.index',
                                'menu_id' => 39,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                    'refunds' => [
                        'name' => 'refunds',
                        'title' => 'Refunds',
                        'icon' => 'uil-bug',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 9,
                        'children' => [
                            'all_refunds' => [
                                'name' => 'all_refunds',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'refunds.index',
                                'menu_id' => 41,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                ]
            ],
            'cms' => [
                'name' => 'cms',
                'title' => 'CMS',
                'icon' => 'uil-window-restore',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 6,
                'children' => [
                    'blogs' => [
                        'name' => 'blogs',
                        'title' => 'Blogs',
                        'icon' => 'uil-window-restore',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 10,
                        'children' => [
                            'all_blogs' => [
                                'name' => 'all_blogs',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'blogs.index',
                                'menu_id' => 43,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                    'pages' => [
                        'name' => 'pages',
                        'title' => 'Pages',
                        'icon' => 'uil-web-grid',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 13,
                        'children' => [
                            'all_pages' => [
                                'name' => 'all_pages',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'pages.index',
                                'menu_id' => 49,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                    'faqs' => [
                        'name' => 'faqs',
                        'title' => 'Faqs',
                        'icon' => 'uil-comments',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 12,
                        'children' => [
                            'all_faqs' => [
                                'name' => 'all_faqs',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'faqs.index',
                                'menu_id' => 47,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                ]
            ],
            'user_experiences' => [
                'name' => 'user_experiences',
                'title' => 'User Experiences',
                'icon' => 'uil-window-restore',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 6,
                'children' => [
                    'ratings' => [
                        'name' => 'ratings',
                        'title' => 'Ratings',
                        'icon' => 'uil-comment-alt-check',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 11,
                        'children' => [
                            'all_ratings' => [
                                'name' => 'all_ratings',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'ratings.index',
                                'menu_id' => 45,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                    'subscriptions' => [
                        'name' => 'subscriptions',
                        'title' => 'Subscriptions',
                        'icon' => 'uil-google-hangouts',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 14,
                        'children' => [
                            'all_subscriptions' => [
                                'name' => 'all_subscriptions',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'subscriptions.index',
                                'menu_id' => 51,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                    'testimonials' => [
                        'name' => 'testimonials',
                        'title' => 'Tesimonials',
                        'icon' => ' uil-print',
                        'url' => '#',
                        'created_by' => 1,
                        'ordering' => 15,
                        'children' => [
                            'all_testimonials' => [
                                'name' => 'all_testimonials',
                                'title' => 'All',
                                'icon' => '',
                                'url' => 'testimonials.index',
                                'menu_id' => 53,
                                'created_by' => 1,
                                'ordering' => 1,
                            ]
                        ]
                    ],
                ]
            ],
            'users' => [
                'name' => 'users',
                'title' => 'Users',
                'icon' => 'uil-users-alt',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 16,
                'children' => [
                    'all_users' => [
                        'name' => 'all_users',
                        'title' => 'All',
                        'icon' => '',
                        'url' => 'users.index',
                        'menu_id' => 55,
                        'created_by' => 1,
                        'ordering' => 1,
                    ],
                    'active_users' => [
                        'name' => 'active_users',
                        'title' => 'Active',
                        'icon' => '',
                        'url' => 'users.active',
                        'menu_id' => 55,
                        'created_by' => 1,
                        'ordering' => 2,
                    ],
                    'inactive_users' => [
                        'name' => 'inactive_users',
                        'title' => 'Inactive',
                        'url' => 'users.inactive',
                        'icon' => '',
                        'menu_id' => 55,
                        'created_by' => 1,
                        'ordering' => 3,
                    ],
                ]
            ],
            'reports' => [
                'name' => 'reports',
                'title' => 'Reports',
                'icon' => 'uil-folder-download',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 17,
                'children' => [
                    'all_reports' => [
                        'name' => 'all_reports',
                        'title' => 'All',
                        'url' => 'reports.index',
                        'icon' => '',
                        'menu_id' => 59,
                        'created_by' => 1,
                        'ordering' => 3,
                    ],
                ]
            ],
            'settings' => [
                'name' => 'settings',
                'title' => 'Settings',
                'icon' => 'uil-circuit',
                'url' => '#',
                'created_by' => 1,
                'ordering' => 18,
                'children' => [
                    'banks' => [
                        'name' => 'banks',
                        'title' => 'Banks',
                        'icon' => '',
                        'url' => 'banks.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 1,
                        'children' => []
                    ],
                    'bank_details' => [
                        'name' => 'bank_details',
                        'title' => 'Bank Details',
                        'icon' => '',
                        'url' => 'bank_details.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 2,
                        'children' => []
                    ],
                    'exchange_rates' => [
                        'name' => 'exchange_rates',
                        'title' => 'Exchange Rates',
                        'icon' => '',
                        'url' => 'exchange_rates.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 3,
                        'children' => []
                    ],
                    'seasons' => [
                        'name' => 'seasons',
                        'title' => 'Seasons',
                        'icon' => '',
                        'url' => 'seasons.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 4,
                        'children' => []
                    ],
                    'countries' => [
                        'name' => 'countries',
                        'title' => 'Countries',
                        'icon' => '',
                        'url' => 'countries.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 5,
                        'children' => []
                    ],
                    'regions' => [
                        'name' => 'regions',
                        'title' => 'Regions',
                        'icon' => '',
                        'url' => 'regions.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 6,
                        'children' => []
                    ],
                    'districts' => [
                        'name' => 'districts',
                        'title' => 'Districts',
                        'icon' => '',
                        'url' => 'districts.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 7,
                        'children' => []
                    ],
                    'locations' => [
                        'name' => 'locations',
                        'title' => 'Locations',
                        'icon' => '',
                        'url' => 'locations.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 8,
                        'children' => []
                    ],
                    'activities' => [
                        'name' => 'activities',
                        'title' => 'Activities',
                        'icon' => '',
                        'url' => 'activities.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 9,
                        'children' => []
                    ],
                    'categories' => [
                        'name' => 'categories',
                        'title' => 'Categories',
                        'icon' => '',
                        'url' => 'categories.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 10,
                        'children' => []
                    ],
                    'addons' => [
                        'name' => 'addons',
                        'title' => 'Addons',
                        'icon' => '',
                        'url' => 'addons.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 11,
                        'children' => []
                    ],
                    'budgets' => [
                        'name' => 'budgets',
                        'title' => 'Budgets',
                        'icon' => '',
                        'url' => 'budgets.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 12,
                        'children' => []
                    ],
                    'permissions' => [
                        'name' => 'permissions',
                        'title' => 'Permissions',
                        'icon' => '',
                        'url' => 'permissions.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 13,
                        'children' => []
                    ],
                    'roles' => [
                        'name' => 'roles',
                        'title' => 'Roles',
                        'icon' => '',
                        'url' => 'roles.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 14,
                        'children' => []
                    ],
                    'menus' => [
                        'name' => 'menus',
                        'title' => 'Menus',
                        'icon' => '',
                        'url' => 'menus.index',
                        'menu_id' => 61,
                        'created_by' => 1,
                        'ordering' => 15,
                        'children' => []
                    ]
                ]
            ],
        ];

        foreach ($menus as $menuKey => $menuData) {
            $menu = Menu::create([
                'name' => $menuData['name'],
                'title' => $menuData['title'],
                'icon' => $menuData['icon'],
                'url' => $menuData['url'],
                'created_by' => $menuData['created_by'],
                'ordering' => $menuData['ordering'],
                'is_active' => true,
                'created_at' => Carbon::now(),
                'uuid' => \Illuminate\Support\Str::orderedUuid()
            ]);

            // Create child menus
            if (!empty($menuData['children'])) {
                foreach ($menuData['children'] as $childKey => $childData) {
                    $multi = Menu::create([
                        'name' => $childData['name'],
                        'title' => $childData['title'],
                        'icon' => $childData['icon'],
                        'url' => $childData['url'],
                        'menu_id' => $menu->id,
                        'created_by' => $childData['created_by'],
                        'ordering' => $childData['ordering'],
                        'is_active' => true,
                        'created_at' => Carbon::now(),
                        'uuid' => \Illuminate\Support\Str::orderedUuid()
                    ]);

                    if (!empty($childData['children'])) {
                        foreach ($childData['children'] as $childKey => $multiData) {
                            Menu::create([
                                'name' => $multiData['name'],
                                'title' => $multiData['title'],
                                'icon' => $multiData['icon'],
                                'url' => $multiData['url'],
                                'menu_id' => $multi->id,
                                'created_by' => $multiData['created_by'],
                                'ordering' => $multiData['ordering'],
                                'is_active' => true,
                                'created_at' => Carbon::now(),
                                'uuid' => \Illuminate\Support\Str::orderedUuid()
                            ]);
                        }
                    }
                }
            }
        }
    }
}
