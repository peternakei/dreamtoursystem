<?php

namespace App\Project\Modules\System\Invoices\Seeders;

use App\Project\Modules\System\Invoices\InvoiceStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Fully Paid' => '#fb8500',
            'Pending' => '#780000',
            'Partial Paid' => '#003049',
            'Cancelled' => '#2a9d8f',
            'Expired' => '#00296b',
            'Refunded' => '#ef233c',
        ];

        foreach ($statuses as $status => $color) {
            InvoiceStatus::create([
                'name' => $status,
                'color' => $color,
            ]);
        }
    }
}
