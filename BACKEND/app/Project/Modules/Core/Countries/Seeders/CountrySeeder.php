<?php

namespace App\Project\Modules\Core\Countries\Seeders;

use App\Project\Modules\Core\Countries\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //regions
        $contents = File::get(public_path('tanzania/countries.json'));
        $countries = json_decode(json: $contents, associative: true);

        foreach ($countries as $country) {
            Country::create([
                'name' => htmlspecialchars($country['name']),
                'code' => $country['code'],
                'created_by' => 1,
            ]);
        }
    }
}
