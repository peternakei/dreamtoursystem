<?php

namespace App\Project\Modules\Core\AgeRanges\Seeders;

use App\Project\Modules\Core\AgeRanges\AgeRange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgeRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranges = [
            '0-5 years' => '#fb8500',
            '6-17 years' => '#780000',
            '18-64 years' => '#780000',
            '65+ years' => '#780000',
        ];

        foreach ($ranges as $range => $color) {
            AgeRange::create([
                'name' => $range,
            ]);
        }
    }
}
