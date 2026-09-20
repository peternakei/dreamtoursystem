<?php

namespace App\Project\Modules\System\Trips\Seeders;

use App\Project\Modules\System\Trips\TripSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripSourceSeeder extends Seeder
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
            TripSource::create([
                'name' => $source,
                'color' => $color,
            ]);
        }
    }
}
