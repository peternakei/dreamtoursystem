<?php

namespace App\Project\Modules\System\Seasons\Seeders;

use App\Project\Modules\System\Seasons\ServiceClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            'Economy' => '#fb8500',
            'Luxury' => '#780000',
            'Semi Luxury' => '#669bbc',
        ];

        foreach ($classes as $season => $color) {
            ServiceClass::create([
                'name' => $season,
            ]);
        }
    }
}
