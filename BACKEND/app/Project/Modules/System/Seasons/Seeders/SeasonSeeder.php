<?php

namespace App\Project\Modules\System\Seasons\Seeders;

use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\SeasonDate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            [
                'name' => 'High',
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
                'created_by' => 1
            ],
            [
                'name' => 'Low',
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
                'created_by' => 1
            ]
        ];

        foreach ($seasons as $season) {
            $save = Season::create([
                'name' => $season['name'],
                'created_by' => $season['created_by']
            ]);
            SeasonDate::create([
                'season_id' => $save->id,
                'start_date' => $season['start_date'],
                'end_date' => $season['end_date'],
                'created_by' => 1
            ]);
        }
    }
}
