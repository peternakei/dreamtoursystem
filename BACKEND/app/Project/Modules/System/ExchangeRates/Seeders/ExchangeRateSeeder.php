<?php

namespace App\Project\Modules\System\ExchangeRates\Seeders;

use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use App\Project\Modules\Core\Users\User;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        // Rate semantics: how many of this currency equals 1 USD (USD is the base).
        // USD itself = 1.0. Adjust through the admin UI as live rates change.
        $rates = [
            'USD' => 1.0,
            'EUR' => 0.92,
            'GBP' => 0.78,
            'TZS' => 2500.0,
        ];

        $userId = User::query()->orderBy('id')->value('id') ?? 1;

        foreach ($rates as $shortName => $rate) {
            $currency = Currency::where('short_name', $shortName)->first();
            if (!$currency) {
                continue;
            }

            ExchangeRate::where('currency_id', $currency->id)
                ->where('is_active', true)
                ->where('rate', '!=', $rate)
                ->update(['is_active' => false, 'updated_by' => $userId]);

            ExchangeRate::firstOrCreate(
                [
                    'currency_id' => $currency->id,
                    'rate' => $rate,
                    'is_active' => true,
                ],
                ['created_by' => $userId]
            );
        }
    }
}
