<?php

namespace App\Project\Modules\Core\PaymentModes\Seeders;

use App\Project\Modules\Core\PaymentModes\PaymentMode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modes = [
            'Cash' => '#218380',
            'Cheque' => '#ff70a6',
            'Bank Transfer' => '#3c096c',
        ];

        foreach ($modes as $type => $code) {
            PaymentMode::create([
                'name' => $type,
                'color' => $code
            ]);
        }
    }
}
