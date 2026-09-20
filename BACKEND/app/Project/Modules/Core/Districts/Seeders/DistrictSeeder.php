<?php

namespace App\Project\Modules\Core\Districts\Seeders;

use App\Project\Modules\Core\Districts\District;
use App\Project\Modules\Core\Regions\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = File::get(public_path('tanzania/districts.json'));
        $districts = json_decode(json: $contents, associative: true);

        foreach ($districts as $district) {

            $region = Region::where(['regionId' => $district['regionID']])->first();

            District::create([
                'name' => htmlspecialchars($district['fullName']),
                'region_id' => $region->id,
                'created_by' => 1,
            ]);
        }
    }
}
