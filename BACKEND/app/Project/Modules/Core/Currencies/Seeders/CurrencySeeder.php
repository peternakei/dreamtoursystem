<?php

namespace App\Project\Modules\Core\Currencies\Seeders;

use App\Project\Modules\Core\Currencies\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            'Tanzania Shilling' => ['code' => 'TZS', 'symbol' => 'Tsh'],
            'United State Dollar' => ['code' => 'USD', 'symbol' => '$'],
            'Euro' => ['code' => 'EUR', 'symbol' => 'EUR'],
            'Pound' => ['code' => 'GBP', 'symbol' => 'GBP'],
        ];

        foreach ($currencies as $currency => $data) {
            Currency::updateOrCreate(
                ['short_name' => $data['code']],
                [
                    'name' => $currency,
                    'symbol' => $data['symbol'],
                ]
            );
        }
    }
}
