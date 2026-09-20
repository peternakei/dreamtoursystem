<?php

namespace App\Project\Modules\Core\Genders\Seeders;

use App\Project\Modules\Core\Genders\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            'Male' => '#218380',
            'Female' => '#ff70a6',
        ];

        foreach ($genders as $type => $code) {
            Gender::create([
                'name' => $type,
                'color' => $code
            ]);
        }
    }
}
