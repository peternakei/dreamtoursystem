<?php

namespace App\Project\Modules\Core\Regions\Seeders;

use App\Project\Modules\Core\Regions\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //regions
        $contents = File::get(public_path('tanzania/regions.json'));
        $regions = json_decode(json: $contents, associative: true);

        foreach ($regions as $region) {
            Region::create([
                'regionID' => $region['regionID'],
                'name' => htmlspecialchars($region['fullName']),
                'country_id' => 214,
                'created_by' => 1,
            ]);
        }
    }
}
