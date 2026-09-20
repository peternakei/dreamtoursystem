<?php

namespace App\Project\Modules\System\Bookings\Seeders;

use App\Project\Modules\System\Bookings\BookingStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Completed' => '#fb8500',
            'Reserved' => '#780000',
            'Confirmed' => '#003049',
            'Cancelled' => '#2a9d8f',
            'Expired' => '#00296b',
        ];

        foreach ($statuses as $status => $color) {
            BookingStatus::create([
                'name' => $status,
                'color' => $color,
            ]);
        }
    }
}
