<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\QuotationPriceLine;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Seasons\Budget;
use App\Project\Modules\System\Seasons\SeasonDate;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Support\Carbon;

class QuotePricingService
{
    public const VAT_RATE = 0.18;

    public function defaultCurrencyId(): ?int
    {
        return Currency::query()
            ->where('short_name', 'USD')
            ->value('id')
            ?? Currency::query()->value('id');
    }

    public function openStatusId(): ?int
    {
        return QuotationStatus::query()
            ->whereRaw('LOWER(name) = ?', ['open'])
            ->value('id')
            ?? QuotationStatus::query()->value('id');
    }

    public function resolveGuestBucket(?int $guestCount): int
    {
        $guestCount = max((int) $guestCount, 1);

        if ($guestCount <= 3) {
            return 2;
        }

        if ($guestCount <= 5) {
            return 4;
        }

        return 6;
    }

    public function resolveSeasonId($startDate): ?int
    {
        if (!$startDate) {
            return SeasonDate::query()->where('is_active', true)->orderBy('id')->value('season_id');
        }

        $date = Carbon::parse($startDate)->toDateString();

        return SeasonDate::query()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('is_active', true)
            ->value('season_id')
            ?? SeasonDate::query()->where('is_active', true)->orderBy('id')->value('season_id');
    }

    public function resolveBudget(?Trip $trip, $startDate, ?int $serviceClassId, ?int $guestCount): ?Budget
    {
        if (!$trip) {
            return null;
        }

        $seasonId = $this->resolveSeasonId($startDate);
        $quantity = $this->resolveGuestBucket($guestCount);

        $query = Budget::query()
            ->where('trip_id', $trip->id)
            ->where('quantity', $quantity)
            ->where('is_active', true);

        if ($seasonId) {
            $query->where('season_id', $seasonId);
        }

        if ($serviceClassId) {
            $query->where('service_class_id', $serviceClassId);
        }

        $budget = $query->first();

        if ($budget) {
            return $budget;
        }

        return Budget::query()
            ->where('trip_id', $trip->id)
            ->where('quantity', $quantity)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }

    public function seedVersionPriceLines(QuotationVersion $version, Inquiry $inquiry, int $userId): void
    {
        $budget = $this->resolveBudget($version->trip, $version->start_date, $version->service_class_id, $version->guest_count);
        $currencyId = $budget?->currency_id ?: $version->currency_id ?: $this->defaultCurrencyId();
        $guestCount = max((int) $version->guest_count, 1);

        $version->priceLines()->delete();

        if ($budget) {
            $version->priceLines()->create([
                'source_type' => Budget::class,
                'source_id' => $budget->id,
                'description' => ($version->trip?->name ?: $version->title ?: 'Safari package') . ' package',
                'traveler_type' => 'Guest',
                'quantity' => $guestCount,
                'unit_price' => $budget->price,
                'total_price' => $guestCount * (float) $budget->price,
                'currency_id' => $currencyId,
                'is_optional' => false,
                'is_visible' => true,
                'sort_order' => 1,
                'created_by' => $userId,
            ]);
        } else {
            $version->priceLines()->create([
                'source_type' => 'manual',
                'description' => ($version->trip?->name ?: $version->title ?: 'Safari package') . ' package',
                'traveler_type' => 'Guest',
                'quantity' => $guestCount,
                'unit_price' => 0,
                'total_price' => 0,
                'currency_id' => $currencyId,
                'is_optional' => false,
                'is_visible' => true,
                'sort_order' => 1,
                'created_by' => $userId,
            ]);
        }

        $this->syncQuotationTotals($version->fresh('quotation'));
    }

    public function syncPriceLines(QuotationVersion $version, array $rows, int $userId): void
    {
        $version->priceLines()->delete();

        collect($rows)
            ->filter(function ($row) {
                return is_array($row) && collect($row)->filter(fn($value) => $value !== null && $value !== '')->isNotEmpty();
            })
            ->values()
            ->each(function (array $row, int $index) use ($version, $userId) {
                $quantity = max((int) ($row['quantity'] ?? 1), 1);
                $unitPrice = (float) ($row['unit_price'] ?? 0);
                $totalPrice = (float) ($row['total_price'] ?? ($quantity * $unitPrice));

                $version->priceLines()->create([
                    'source_type' => $row['source_type'] ?: 'manual',
                    'source_id' => $row['source_id'] ?: null,
                    'description' => $row['description'] ?: 'Line item',
                    'traveler_type' => $row['traveler_type'] ?: null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'currency_id' => $row['currency_id'] ?: $version->currency_id,
                    'is_optional' => (bool) ($row['is_optional'] ?? false),
                    'is_visible' => (bool) ($row['is_visible'] ?? false),
                    'sort_order' => $index + 1,
                    'created_by' => $userId,
                ]);
            });

        $this->syncQuotationTotals($version->fresh('quotation'));
    }

    public function syncQuotationTotals(QuotationVersion $version): void
    {
        $baseCurrencyId = $version->currency_id ?: $this->defaultCurrencyId();

        // Rate semantics in exchange_rates: how many of this currency equals 1 USD.
        // Convert from currency X to currency Y: amount * rateY / rateX.
        $rates = ExchangeRate::where('is_active', true)
            ->pluck('rate', 'currency_id')
            ->map(fn ($rate) => (float) $rate);

        $usdId = Currency::where('short_name', 'USD')->value('id');
        if ($usdId && !$rates->has($usdId)) {
            $rates[$usdId] = 1.0;
        }

        $baseRate = $rates->get($baseCurrencyId);

        $amount = (float) $version->priceLines()
            ->where('is_optional', false)
            ->get(['total_price', 'currency_id'])
            ->sum(function (QuotationPriceLine $line) use ($baseCurrencyId, $baseRate, $rates) {
                $lineTotal = (float) $line->total_price;
                $lineCurrencyId = $line->currency_id ?: $baseCurrencyId;

                if ($lineCurrencyId === $baseCurrencyId) {
                    return $lineTotal;
                }

                $lineRate = $rates->get($lineCurrencyId);
                if (!$lineRate || $lineRate <= 0 || !$baseRate || $baseRate <= 0) {
                    return $lineTotal;
                }

                return $lineTotal * $baseRate / $lineRate;
            });

        $vat = $version->vat_enabled ? $amount * self::VAT_RATE : 0.0;
        $total = $amount + $vat;

        $version->update([
            'amount' => $amount,
            'vat_amount' => $vat,
            'total_amount' => $total,
        ]);

        $version->quotation->update([
            'current_version_id' => $version->id,
            'amount' => $amount,
            'vat_amount' => $vat,
            'total_amount' => $total,
            'currency_id' => $baseCurrencyId ?: $version->quotation->currency_id,
            'updated_by' => $version->updated_by ?: $version->created_by,
        ]);
    }
}
