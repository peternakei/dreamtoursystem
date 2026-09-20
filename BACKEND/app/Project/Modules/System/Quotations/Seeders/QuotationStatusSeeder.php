<?php

namespace App\Project\Modules\System\Quotations\Seeders;

use App\Project\Modules\System\Quotations\QuotationStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuotationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Won' => '#fb8500',
            'Open' => '#780000',
            'Lost' => '#003049',
        ];

        foreach ($statuses as $status => $color) {
            QuotationStatus::create([
                'name' => $status,
                'color' => $color,
            ]);
        }
    }
}
