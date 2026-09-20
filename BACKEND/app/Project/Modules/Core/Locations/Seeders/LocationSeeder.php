<?php

namespace App\Project\Modules\Core\Locations\Seeders;

use App\Project\Modules\Core\Locations\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Tanzania Mainland' => '#218380',
            'Zanzibar' => '#ff70a6',
        ];

        foreach ($locations as $type => $code) {
            Location::create([
                'name' => $type,
                'created_by' => 1
            ]);
        }
    }
}
