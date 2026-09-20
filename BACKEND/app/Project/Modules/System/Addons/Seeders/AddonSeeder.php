<?php

namespace App\Project\Modules\System\Addons\Seeders;

use App\Project\Modules\System\Addons\Addon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addons = [
            'WiFi' => '#003049',
            'Arrival Day and Welcome – Pickup from airport and hotel transfer with optional briefing.' => '#76520e',
            '4X4 Custom built – Toyota Land Cruiser.' => '#6f1d1b',
            'A Special Safari Hamper' => '#2a9d8f',
            'Soft drinks, Coffee/tea and Water during game drives' => '#386641',
            'TO/FROM Kilimanjaro International or Arusha Airport - One arrival Transfer and One' => '#7209b7',
            'Departure Transfer' => '#0f4c5c',
            'Lunch box for 4 days' => '#81b29a',
        ];

        foreach ($addons as $addon => $color) {
            Addon::create([
                'name' => htmlspecialchars($addon),
                'created_by' => 1
            ]);
        }
    }
}
