<?php

namespace App\Project\Modules\System\Bookings\Seeders;

use App\Project\Modules\System\Vehicles\Vehicle;
use Database\Seeders\Support\PublicAssetImporter;
use Illuminate\Database\Seeder;

/**
 * Seeds the operator's safari fleet. The marketing site doesn't expose
 * vehicles directly, but the proposal PDF lists them on the dedicated
 * Vehicles page — so we seed a realistic catalogue that lets the quote
 * builder demonstrate the full vehicle picker.
 *
 * Previously this seeder read from config('company.vehicles'). That source
 * is now used only as a UI fallback; the canonical fleet lives in the DB.
 */
class VehicleSeeder extends Seeder
{
    protected array $vehicles = [
        [
            'name' => '4x4 Safari Land Cruiser',
            'capacity' => 'Up to 6 guests',
            'description' => 'Custom-built Toyota Land Cruiser with pop-up roof for game viewing, charging ports, fridge, binoculars, and a stocked cooler box.',
            'cover' => 'vehicle-landcruiser.webp',
        ],
        [
            'name' => '4x4 Game Viewer',
            'capacity' => 'Up to 7 guests',
            'description' => 'Heavy-duty 4x4 Land Cruiser game viewer with raised roof and panoramic windows, fitted for long days in the bush and rough park tracks.',
            'cover' => 'vehicle-game-viewer.webp',
        ],
        [
            'name' => 'Open-Top Game Drive Vehicle',
            'capacity' => 'Up to 8 guests',
            'description' => 'Open-sided game drive vehicle with stadium seating and a sun canopy — unmatched 360° views for photography and big-cat sightings.',
            'cover' => 'vehicle-open-top.webp',
        ],
        [
            'name' => 'Extended Tour Van',
            'capacity' => 'Up to 9 guests',
            'description' => 'Air-conditioned extended van for transfer days and group movements between camps, airstrips, and Stone Town.',
            'cover' => 'vehicle-tour-van.webp',
        ],
        [
            'name' => 'Private Sedan Transfer',
            'capacity' => 'Up to 3 guests',
            'description' => 'Comfortable air-conditioned sedan for airport transfers, in-town shuttling, and short Stone Town drives.',
            'cover' => null,
        ],
        [
            'name' => 'Light Aircraft Charter',
            'capacity' => 'Up to 12 guests',
            'description' => 'Charter flights connecting Arusha, the Serengeti airstrips, Selous, Ruaha, and Zanzibar — operated by Coastal Aviation and partners.',
            'cover' => null,
        ],
    ];

    public function run(): void
    {
        $importer = new PublicAssetImporter($this->command);
        $importer->convertAll();

        $userId = 1;

        foreach ($this->vehicles as $i => $data) {
            $vehicle = Vehicle::firstOrCreate(
                ['name' => $data['name']],
                [
                    'capacity' => $data['capacity'] ?? null,
                    'description' => $data['description'] ?? null,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                    'created_by' => $userId,
                ]
            );

            $vehicle->fill([
                'capacity' => $data['capacity'] ?? null,
                'description' => $data['description'] ?? null,
                'sort_order' => $i + 1,
                'is_active' => true,
            ])->save();

            if (!empty($data['cover'])) {
                $importer->attach($vehicle, $data['cover'], title: $vehicle->name);
            }
        }
    }
}
