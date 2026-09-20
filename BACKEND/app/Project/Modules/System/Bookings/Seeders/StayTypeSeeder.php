<?php

namespace App\Project\Modules\System\Bookings\Seeders;

use App\Project\Modules\System\Accommodations\StayType;
use Illuminate\Database\Seeder;

class StayTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Lodge', 'color' => '#10b981', 'description' => 'Permanent lodge with full amenities.'],
            ['name' => 'Tented Camp', 'color' => '#f59e0b', 'description' => 'Permanent tented camp, comfortable wilderness setting.'],
            ['name' => 'Mobile Camp', 'color' => '#ef4444', 'description' => 'Seasonal camp that follows wildlife migrations.'],
            ['name' => 'Hotel', 'color' => '#3b82f6', 'description' => 'Urban or town hotel.'],
            ['name' => 'Bush Camp', 'color' => '#8b5cf6', 'description' => 'Rustic camp in remote bush location.'],
            ['name' => 'Beach Resort', 'color' => '#06b6d4', 'description' => 'Coastal resort property.'],
            ['name' => 'Villa', 'color' => '#ec4899', 'description' => 'Private villa or guesthouse.'],
            ['name' => 'Permanent Camp', 'color' => '#84cc16', 'description' => 'Year-round permanent tented camp.'],
        ];

        foreach ($defaults as $i => $data) {
            StayType::firstOrCreate(
                ['name' => $data['name']],
                [
                    'description' => $data['description'],
                    'color' => $data['color'],
                    'sort_order' => $i,
                ]
            );
        }
    }
}
