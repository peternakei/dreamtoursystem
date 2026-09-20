<?php

namespace App\Project\Modules\System\Refunds\Seeders;

use App\Project\Modules\System\Refunds\RefundStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefundStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Completed' => '#fb8500',
            'Pending' => '#780000',
            'Cancelled' => '#003049',
        ];

        foreach ($statuses as $status => $color) {
            RefundStatus::create([
                'name' => $status,
                'color' => $color,
            ]);
        }
    }
}
