<?php

namespace App\Project\Modules\Core\DiscountTypes\Seeders;

use App\Project\Modules\Core\DiscountTypes\DiscountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Fixed' => '#218380',
            'Percentage' => '#ff70a6',
        ];

        foreach ($types as $type => $code) {
            DiscountType::create([
                'name' => $type,
                'color' => $code
            ]);
        }
    }
}
