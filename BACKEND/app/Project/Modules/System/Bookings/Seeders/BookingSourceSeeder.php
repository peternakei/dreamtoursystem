<?php

namespace App\Project\Modules\System\Bookings\Seeders;

use App\Project\Modules\System\Bookings\BookingSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Management' => '#003049',
            'Portal' => '#2a9d8f',
        ];

        foreach ($sources as $source => $color) {
            BookingSource::create([
                'name' => $source,
                'color' => $color,
            ]);
        }
    }
}
