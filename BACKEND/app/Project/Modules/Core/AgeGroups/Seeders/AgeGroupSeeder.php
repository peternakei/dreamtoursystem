<?php

namespace App\Project\Modules\Core\AgeGroups\Seeders;

use App\Project\Modules\Core\AgeGroups\AgeGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            'Child' => '#fb8500',
            'Adult' => '#780000',
        ];

        foreach ($groups as $group => $color) {
            AgeGroup::create([
                'name' => $group,
            ]);
        }
    }
}
