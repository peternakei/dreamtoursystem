<?php

namespace App\Project\Modules\System\Trips\Seeders;

use App\Project\Modules\System\Trips\TripStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Approved' => '#fb8500',
            'Pending' => '#780000',
            'Cancelled' => '#003049',
            'Completed' => '#eb5e28',
        ];

        foreach ($statuses as $status => $color) {
            TripStatus::create([
                'name' => $status,
                'color' => $color,
            ]);
        }
    }
}
