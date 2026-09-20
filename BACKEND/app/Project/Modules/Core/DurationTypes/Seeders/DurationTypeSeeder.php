<?php

namespace App\Project\Modules\Core\DurationTypes\Seeders;

use App\Project\Modules\Core\DurationTypes\DurationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DurationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Minutes' => '#218380',
            'Hours' => '#ff70a6',
            'Days' => '#ff70a6',
        ];

        foreach ($types as $type => $code) {
            DurationType::create([
                'name' => $type,
            ]);
        }
    }
}
