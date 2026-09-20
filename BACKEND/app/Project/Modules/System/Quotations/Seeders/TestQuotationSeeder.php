<?php

namespace App\Project\Modules\System\Quotations\Seeders;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Genders\Gender;
use App\Project\Modules\Core\Locations\Location;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Seeds a realistic Tourist → Inquiry → Quotation chain on top of the
 * public-site library. Each tourist gets one inquiry against a real seeded
 * trip; the QuoteBuilderService then materialises the matching
 * Quotation + QuotationVersion + QuotationDay + QuotationDayActivity +
 * QuotationPriceLine + QuotationTerm + QuotationPaymentTerm rows by reading
 * from the seeded trip, addons, budgets, and itinerary.
 *
 * The three scenarios cover the operator's three quotation states:
 *  - Open  — fresh proposal sent to the prospect, awaiting reply
 *  - Won   — converted booking with the public URL enabled
 *  - Lost  — declined / lost to a competitor, kept for archive
 *
 * Idempotent on the inquiry layer (one inquiry per tour title / tourist).
 * Quotation creation is only attempted when the inquiry has no quotation
 * yet so re-runs don't keep adding versions.
 */
class TestQuotationSeeder extends Seeder
{
    /**
     * Per-tourist scenario data. Each entry produces 1 tourist + 1 inquiry
     * + 1 quotation tied to the listed trip slug.
     */
    protected array $scenarios = [
        [
            'tourist' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+15551234567',
                'country' => 'United Kingdom',
                'gender' => 'Male',
                'address' => '221B Baker Street, London',
            ],
            'inquiry' => [
                'tour_title' => 'Northern Tanzania Classic — Honeymoon',
                'description' => '5-day classic northern circuit safari for two adults with comfortable lodges, private game drives, and a small honeymoon touch.',
                'months_away' => 2,
                'duration_days' => 5,
                'guests' => 2,
                'budget' => 8000,
                'service_class' => 'Semi Luxury',
                'source' => 'website',
                'client_message' => 'Honeymoon trip — dietary preferences are pescatarian, would love a private game-drive vehicle.',
            ],
            'trip_slug' => 'safari-zanzibar-experience',
            'quotation' => [
                'status' => 'Open',
                'title' => 'Northern Tanzania Classic — Honeymoon Edition',
                'subtitle' => 'A five-day journey through Tarangire, Ngorongoro, and the Serengeti',
                'introduction' => 'We have shaped this five-day proposal around your honeymoon, blending iconic wildlife encounters with romantic, intimate accommodations.',
                'public_url_enabled' => true,
                'vat_enabled' => true,
            ],
        ],
        [
            'tourist' => [
                'name' => 'Maria Schmidt',
                'email' => 'maria.schmidt@example.com',
                'phone' => '+4915123456789',
                'country' => 'Germany',
                'gender' => 'Female',
                'address' => 'Friedrichstraße 50, Berlin',
            ],
            'inquiry' => [
                'tour_title' => 'Serengeti Migration Photography Safari',
                'description' => 'Fly-in Serengeti safari targeted at the Mara River crossing window with daily game drives.',
                'months_away' => 4,
                'duration_days' => 5,
                'guests' => 4,
                'budget' => 14000,
                'service_class' => 'Luxury',
                'source' => 'referral',
                'client_message' => 'Group of four photographers; prefer mobile camps that follow the migration herds.',
            ],
            'trip_slug' => 'serengeti-migration-safari',
            'quotation' => [
                'status' => 'Won',
                'title' => 'Serengeti Migration — Group of Four',
                'subtitle' => 'A five-day fly-in mobile-camp safari aligned with the Mara crossings',
                'introduction' => 'We have built this proposal around the peak migration window with mobile-camp positioning to maximise your time at the river crossings.',
                'public_url_enabled' => true,
                'vat_enabled' => true,
            ],
        ],
        [
            'tourist' => [
                'name' => 'David Park',
                'email' => 'david.park@example.com',
                'phone' => '+12127345678',
                'country' => 'United States',
                'gender' => 'Male',
                'address' => '350 5th Ave, New York',
            ],
            'inquiry' => [
                'tour_title' => 'Zanzibar Honeymoon Beach Escape',
                'description' => 'Five-day private beach villa stay with a Stone Town day, Mnemba snorkelling, and a sunset dhow cruise.',
                'months_away' => 3,
                'duration_days' => 5,
                'guests' => 2,
                'budget' => 6000,
                'service_class' => 'Semi Luxury',
                'source' => 'social',
                'client_message' => 'Honeymooners — celebrating our 1st anniversary, would love a private candlelit dinner on the beach.',
            ],
            'trip_slug' => 'honeymoon-zanzibar-retreat',
            'quotation' => [
                'status' => 'Lost',
                'title' => 'Zanzibar Honeymoon — Beachfront Villa',
                'subtitle' => 'A five-day spice-island retreat with Nungwi villas, Stone Town, and Mnemba snorkelling',
                'introduction' => 'A relaxed honeymoon proposal blending UNESCO-listed Stone Town with private beach villa nights at Nungwi.',
                'public_url_enabled' => false,
                'vat_enabled' => false,
            ],
        ],
    ];

    public function run(): void
    {
        $userId = 1;
        $countryByName = Country::query()->pluck('id', 'name');
        $genderByName = Gender::query()->pluck('id', 'name');
        $serviceClassByName = ServiceClass::query()->pluck('id', 'name');
        $defaultCountryId = Country::query()->value('id');
        $defaultGenderId = Gender::query()->value('id');
        $statusByName = QuotationStatus::query()
            ->get()
            ->mapWithKeys(fn ($s) => [strtolower($s->name) => $s->id]);

        /** @var QuoteBuilderService $builder */
        $builder = app(QuoteBuilderService::class);

        foreach ($this->scenarios as $scenario) {
            $tourist = Tourist::firstOrCreate(
                ['email' => $scenario['tourist']['email']],
                [
                    'tourist_number' => 'TST-' . strtoupper(Str::random(6)),
                    'name' => $scenario['tourist']['name'],
                    'phone' => $scenario['tourist']['phone'],
                    'country_id' => $countryByName[$scenario['tourist']['country']] ?? $defaultCountryId,
                    'gender_id' => $genderByName[$scenario['tourist']['gender']] ?? $defaultGenderId,
                    'address' => $scenario['tourist']['address'],
                    'created_by' => $userId,
                ]
            );

            $serviceClassId = $serviceClassByName[$scenario['inquiry']['service_class']] ?? null;
            $fromDate = Carbon::now()->addMonths($scenario['inquiry']['months_away'])->startOfDay();
            $toDate = $fromDate->copy()->addDays(max(0, $scenario['inquiry']['duration_days'] - 1));

            $inquiry = Inquiry::firstOrCreate(
                ['tourist_id' => $tourist->id, 'tour_title' => $scenario['inquiry']['tour_title']],
                [
                    'description' => $scenario['inquiry']['description'],
                    'from_date' => $fromDate->toDateString(),
                    'to_date' => $toDate->toDateString(),
                    'destinations' => $this->resolveDestinationIdsForTrip($scenario['trip_slug']),
                    'guests' => $scenario['inquiry']['guests'],
                    'budget' => $scenario['inquiry']['budget'],
                    'source' => $scenario['inquiry']['source'],
                    'service_class_id' => $serviceClassId,
                    'received_at' => Carbon::now()->subDays(7),
                    'client_message' => $scenario['inquiry']['client_message'],
                    'created_by' => $userId,
                ]
            );

            $existingQuotation = Quotation::query()->where('inquiry_id', $inquiry->id)->first();
            if ($existingQuotation) {
                $this->command?->info(sprintf(
                    'TestQuotationSeeder: inquiry "%s" already has quotation #%s — skipping.',
                    $scenario['inquiry']['tour_title'],
                    $existingQuotation->quotation_number
                ));
                continue;
            }

            $trip = Trip::query()->where('slug', $scenario['trip_slug'])->first();

            $version = $builder->createFromInquiry($inquiry, [
                'title' => $scenario['quotation']['title'],
                'subtitle' => $scenario['quotation']['subtitle'],
                'introduction' => $scenario['quotation']['introduction'],
                'trip_id' => $trip?->uuid,
                'currency_id' => null,
                'service_class_id' => $serviceClassId,
            ], $userId);

            $statusId = $statusByName[strtolower($scenario['quotation']['status'])]
                ?? $statusByName['open']
                ?? null;

            if ($statusId) {
                $version->quotation->update([
                    'quotation_status_id' => $statusId,
                    'updated_by' => $userId,
                ]);
            }

            $version->update([
                'public_token' => $scenario['quotation']['public_url_enabled']
                    ? ($version->public_token ?: Str::random(48))
                    : null,
                'public_url_enabled' => $scenario['quotation']['public_url_enabled'],
                'vat_enabled' => $scenario['quotation']['vat_enabled'],
            ]);

            $fresh = $version->fresh();
            $this->command?->info(sprintf(
                'Quotation seeded — tourist=%s status=%s ref=%s public=%s',
                $tourist->name,
                $scenario['quotation']['status'],
                $fresh->reference_number,
                $fresh->public_token ?: '(disabled)'
            ));
        }
    }

    /**
     * Resolve the destination IDs that a trip is linked to so we can store
     * them on the inquiry exactly the way the inquiry form would when an
     * admin builds the request manually.
     */
    private function resolveDestinationIdsForTrip(string $slug): array
    {
        $trip = Trip::query()->where('slug', $slug)->first();
        if (!$trip) {
            return [Destination::query()->value('id')] ?? [];
        }

        return $trip->destinations()
            ->where('is_active', true)
            ->pluck('destination_id')
            ->all();
    }
}
