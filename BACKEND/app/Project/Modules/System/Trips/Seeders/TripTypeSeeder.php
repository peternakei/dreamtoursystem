<?php

namespace App\Project\Modules\System\Trips\Seeders;

use App\Project\Modules\System\Trips\TripType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Individual' => '#fb8500',
            'Group' => '#780000',
        ];

        foreach ($types as $type => $color) {
            TripType::create([
                'name' => $type,
                'color' => $color,
            ]);
        }
    }
}
