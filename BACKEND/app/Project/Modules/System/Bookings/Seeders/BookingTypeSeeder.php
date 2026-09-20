<?php

namespace App\Project\Modules\System\Bookings\Seeders;

use App\Project\Modules\System\Bookings\BookingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Individual' => '#fb8500',
            'Group' => '#780000',
        ];

        foreach ($statuses as $status => $color) {
            BookingType::create([
                'name' => $status,
                'color' => $color,
            ]);
        }
    }
}
