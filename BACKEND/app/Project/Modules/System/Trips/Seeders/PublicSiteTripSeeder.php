<?php

namespace App\Project\Modules\System\Trips\Seeders;

use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Addons\Addon;
use App\Project\Modules\Core\AgeGroups\AgeGroup;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Seasons\Budget;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Category;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripAddon;
use App\Project\Modules\System\Trips\TripCategory;
use App\Project\Modules\System\Trips\TripDay;
use App\Project\Modules\System\Trips\TripDayActivity;
use App\Project\Modules\System\Trips\TripDestination;
use App\Project\Modules\System\Trips\TripGroup;
use App\Project\Modules\System\Trips\TripPoint;
use App\Project\Modules\System\Trips\TripPrice;
use App\Project\Modules\System\Trips\TripSource;
use App\Project\Modules\System\Trips\TripStatus;
use App\Project\Modules\System\Trips\TripType;
use Database\Seeders\Support\PublicAssetImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Seeds the seven trips listed in lib/mock-data/trips.ts on the public
 * Seren Blue Safaris site. Each trip lands in the DB with:
 *
 *  - the core Trip row (with from/to dates, duration, default type/status),
 *  - one or more TripGroup variants (Standard / Luxury),
 *  - TripPrice rows in USD against an Adult age group,
 *  - TripDestination + TripCategory pivots linking to the public-site library,
 *  - sequential TripDay rows mirroring the public itinerary, and
 *  - TripPoint rows for each inclusion (prefixed "Included: …") and
 *    exclusion (prefixed "Not Included: …") — the schema has no type column
 *    so the prefix is the convention.
 *
 * Asset attachments (cover + gallery) are registered through
 * PublicAssetImporter, which produces compressed WebP files at seed time.
 *
 * Idempotent — upserts trips by slug.
 */
class PublicSiteTripSeeder extends Seeder
{
    /**
     * Default departure window for seeded trips — first day of next month.
     * Trips are an evergreen catalogue, so this is purely illustrative.
     */
    private const DEFAULT_DEPARTURE = '+1 month';

    protected array $trips = [
        [
            'name' => 'Mikumi National Park Day Trip',
            'slug' => 'mikumi-national-park-day-trip',
            'description' => "Experience the thrill of a real African safari in a single unforgettable day. Designed for travelers seeking adventure beyond Zanzibar's turquoise shores, this fly-in safari journey takes you deep into the heart of Mikumi National Park — home to elephants, lions, giraffes, zebras, buffalo, and breathtaking savannah landscapes.\n\nOften referred to as the \"Mini Serengeti,\" Mikumi offers an authentic safari experience with abundant wildlife and easy accessibility, making it the perfect escape for a short yet immersive journey into Tanzania's untamed wilderness.",
            'duration_days' => 1,
            'duration_nights' => 0,
            'destinations' => ['Mikumi National Park'],
            'categories' => ['First-Time Safaris'],
            'cover' => 'trip-mikumi-cover.webp',
            'gallery' => ['destination-mikumi.webp', 'gallery-elephants.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 300, 'size' => 12, 'color' => '#10b981'],
                ['name' => 'Luxury',   'price' => 450, 'size' => 6,  'color' => '#f59e0b'],
            ],
            'inclusions' => [
                'Return flight from Zanzibar',
                'All airport transfers',
                'Park entry fees',
                'English-speaking safari guide',
                '4x4 pop-up roof safari vehicle',
                'Packed lunch & bottled water',
            ],
            'exclusions' => [
                'Personal travel insurance',
                'Drinks (other than water)',
                'Personal expenses',
                'Tips & gratuities',
            ],
            'highlights' => [
                'Fly-in safari from Zanzibar to Mikumi National Park',
                "Two game drives in the 'Mini Serengeti'",
                'Spot elephants, lions, giraffes, zebras and buffalo',
                'Bush lunch in the heart of the savannah',
            ],
            'itinerary' => [
                [
                    'title' => 'Day 1 — Zanzibar ⇄ Mikumi Fly-In Safari',
                    'description' => "05:30 — Hotel pick-up in Zanzibar and transfer to the airport.\n07:00 — Scenic flight to Mikumi airstrip.\n08:30 — Morning game drive through Mikumi National Park with your guide.\n12:30 — Bush lunch surrounded by the savannah.\n13:30 — Afternoon game drive: elephants, giraffes, wildebeest, hippos.\n15:30 — Transfer back to the airstrip.\n16:00 — Return flight to Zanzibar.\n18:00 — Hotel transfer and end of safari.",
                ],
            ],
        ],

        [
            'name' => 'Selous Game Reserve Escape',
            'slug' => 'selous-game-reserve-escape',
            'description' => "Discover one of Africa's largest untouched wilderness areas — Nyerere National Park, formerly Selous Game Reserve. This day trip blends thrilling game drives with serene boat safaris along the mighty Rufiji River, where hippos, crocodiles, and elephants gather at the water's edge.\n\nIdeal for travelers chasing raw, untamed Africa, Selous offers an intimate alternative to the busier northern parks, with expert guides revealing the quiet rhythm of the bush.",
            'duration_days' => 1,
            'duration_nights' => 0,
            'destinations' => ['Nyerere / Selous Game Reserve'],
            'categories' => ['First-Time Safaris'],
            'cover' => 'trip-selous-cover.webp',
            'gallery' => ['destination-selous.webp', 'gallery-elephants.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 89, 'size' => 8, 'color' => '#10b981'],
            ],
            'inclusions' => [
                'Hotel pick-up & drop-off',
                'Game drives in a custom 4x4 vehicle',
                'Boat safari on the Rufiji River',
                'Park entry fees',
                'Professional safari guide',
                'Bottled water & bush picnic lunch',
            ],
            'exclusions' => [
                'Personal travel insurance',
                'Alcoholic beverages',
                'Tips & gratuities',
                'Souvenirs & personal expenses',
            ],
            'highlights' => [
                'Day trip to Africa\'s largest game reserve',
                'Boat safari on the Rufiji River',
                'Game drives through open plains and miombo woodlands',
                'Bush picnic lunch in the wilderness',
            ],
            'itinerary' => [
                [
                    'title' => 'Day 1 — Nyerere / Selous Day Safari',
                    'description' => "05:00 — Hotel pick-up and scenic drive to Nyerere National Park.\n09:00 — Park entry, formalities, and start of the wildlife adventure.\n09:30 — Morning game drive in search of lions, elephants and giraffes.\n12:30 — Bush lunch with savannah views.\n14:00 — River boat safari on the Rufiji for hippos, crocodiles and birds.\n16:30 — Return drive begins.\n19:30 — Hotel drop-off.",
                ],
            ],
        ],

        [
            'name' => 'Safari & Zanzibar Experience',
            'slug' => 'safari-and-zanzibar-experience',
            'description' => "The ultimate Tanzania duo — pair an authentic Northern Circuit safari with the laid-back charm of Zanzibar's spice-scented coast. Watch the Big Five roam the Serengeti, descend into the world's largest intact caldera at Ngorongoro, then unwind on powder-white beaches lapped by the Indian Ocean.\n\nThis seven-day journey is designed for travelers who refuse to choose between wilderness and ocean, weaving game drives, cultural encounters, and pure relaxation into one seamless adventure.",
            'duration_days' => 7,
            'duration_nights' => 6,
            'destinations' => ['Serengeti National Park', 'Ngorongoro Crater', 'Tarangire National Park', 'Stone Town', 'Nungwi Beach'],
            'categories' => ['Safari & Zanzibar'],
            'cover' => 'trip-safari-zanzibar-cover.webp',
            'gallery' => ['category-safari-zanzibar.webp', 'destination-serengeti.webp', 'gallery-nungwi.webp', 'gallery-mnemba.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 1200, 'size' => 10, 'color' => '#06b6d4'],
                ['name' => 'Luxury',   'price' => 1800, 'size' => 6,  'color' => '#f59e0b'],
            ],
            'inclusions' => [
                'All domestic flights & transfers',
                'Accommodation in selected lodges & resorts',
                'Full-board safari & half-board beach',
                'All park entry & conservation fees',
                'Game drives in 4x4 safari vehicles',
                'Stone Town & spice farm guided tour',
            ],
            'exclusions' => [
                'International flights',
                'Tanzania visa fees',
                'Premium drinks & spa treatments',
                'Tips for guides & lodge staff',
            ],
            'highlights' => [
                'Three Northern Circuit parks plus beach',
                'Tarangire baobab valleys and elephant herds',
                'Serengeti plains game drives',
                'Ngorongoro Crater descent',
                'Stone Town walking tour and spice farm',
                'Beachfront resort nights in Zanzibar',
            ],
            'itinerary' => [
                ['title' => 'Day 1 — Arrival in Arusha',       'description' => 'Touch down at Kilimanjaro Airport, met by your driver-guide and transferred to your boutique hotel in Arusha for a relaxed welcome dinner and trip briefing.'],
                ['title' => 'Day 2 — Tarangire National Park', 'description' => 'After breakfast, drive to Tarangire for a game drive through baobab valleys famed for towering elephant herds and abundant birdlife. Overnight near the park.'],
                ['title' => 'Day 3 — Into the Serengeti',      'description' => 'Travel to the central Serengeti and begin afternoon game drives across the legendary plains. Overnight in tented camp.'],
                ['title' => 'Day 4 — Migration Country',       'description' => 'Full day exploring herds of wildebeest, zebra, and the predators that follow them. Picnic lunch in the bush. Overnight in tented camp.'],
                ['title' => 'Day 5 — Ngorongoro Crater',       'description' => 'Descend into the crater for a full-day game drive among the densest wildlife in Africa, then transfer to your crater-rim lodge.'],
                ['title' => 'Day 6 — Fly to Zanzibar',         'description' => 'Scenic flight to the spice island and transfer to your beachfront resort for sunset toasts and a relaxed evening.'],
                ['title' => 'Day 7 — Stone Town & Departure',  'description' => 'Guided walk through UNESCO-listed Stone Town and a spice farm, then evening transfer to Zanzibar International Airport for your departure flight.'],
            ],
        ],

        [
            'name' => 'Serengeti Migration Safari',
            'slug' => 'serengeti-migration-safari',
            'description' => "Witness the greatest wildlife spectacle on Earth — the Great Migration — as over a million wildebeest and zebra thunder across the endless Serengeti plains. Time your visit to the river crossings or the calving season for some of the most dramatic moments in the natural world.\n\nThis five-day safari combines expert-guided game drives, mobile camps that follow the herds, and unforgettable sundowners over the savannah.",
            'duration_days' => 5,
            'duration_nights' => 4,
            'destinations' => ['Serengeti National Park'],
            'categories' => ['First-Time Safaris', 'Luxury Safaris'],
            'cover' => 'destination-serengeti.webp',
            'gallery' => ['gallery-zebra.webp', 'gallery-elephants.webp', 'gallery-girrafe.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 2500, 'size' => 8, 'color' => '#10b981'],
                ['name' => 'Luxury',   'price' => 3800, 'size' => 4, 'color' => '#f59e0b'],
            ],
            'inclusions' => [
                'Domestic flights to/from Serengeti',
                'Full-board mobile camp accommodation',
                'Daily game drives with expert guide',
                'All park & conservation fees',
                'Private 4x4 safari vehicle',
                'Sundowner drinks on the plains',
            ],
            'exclusions' => [
                'International flights',
                'Tanzania visa fees',
                'Hot-air balloon safari (optional)',
                'Tips & gratuities',
            ],
            'highlights' => [
                'Fly directly into the Serengeti — no long road days',
                'Mobile tented camp positioned with the migration',
                'Mara River crossing window (Jul – Oct)',
                'Sundowner drinks on the plains each evening',
            ],
            'itinerary' => [
                ['title' => 'Day 1 — Arrival & Transfer',     'description' => 'Arrive at Kilimanjaro Airport in Arusha, met on arrival and connected to a light aircraft into the Serengeti. Settle into your mobile tented camp by the afternoon for a brief sundowner game drive.'],
                ['title' => 'Day 2 — Central Serengeti',      'description' => "Full-day game drive exploring the Seronera region's big cats, resident wildlife and resident plains game. Return to camp for dinner under the stars."],
                ['title' => 'Day 3 — Following the Herds',    'description' => 'Track the migration through the northern plains, with picnic lunch on a rocky kopje and an extended afternoon photographic drive. Overnight at mobile camp.'],
                ['title' => 'Day 4 — Mara River Crossings',   'description' => 'Position at the riverbank to witness the iconic crossings during peak season; otherwise track resident prides and herds nearby. Sundowner drinks on the plains.'],
                ['title' => 'Day 5 — Final Drive & Departure','description' => 'Morning game drive followed by a light aircraft flight back to Arusha for your onward journey or international departure.'],
            ],
        ],

        [
            'name' => 'Ngorongoro Crater Expedition',
            'slug' => 'ngorongoro-crater-expedition',
            'description' => "Descend into a natural wonder — the world's largest intact volcanic caldera and a self-contained Eden teeming with the Big Five year-round. Hemmed in by 600-meter walls, the crater floor concentrates an extraordinary density of wildlife into a single, surreal landscape.\n\nThree perfectly paced days deliver one of Africa's most iconic safaris, with luxury lodging perched on the rim.",
            'duration_days' => 3,
            'duration_nights' => 2,
            'destinations' => ['Ngorongoro Crater'],
            'categories' => ['Luxury Safaris'],
            'cover' => 'destination-ngorongoro.webp',
            'gallery' => ['gallery-luxury-lodge.webp', 'gallery-elephants.webp', 'gallery-maasai.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 1800, 'size' => 6, 'color' => '#f59e0b'],
            ],
            'inclusions' => [
                'Private 4x4 transfers from Arusha',
                'Two nights luxury crater-rim lodge',
                'Full-board meals & house drinks',
                'All park & crater entry fees',
                'Professional safari guide',
                'Maasai village cultural visit',
            ],
            'exclusions' => [
                'International flights',
                'Tanzania visa fees',
                'Premium wines & spirits',
                'Tips & gratuities',
            ],
            'highlights' => [
                'Stay perched on the rim of the world\'s largest intact caldera',
                'Full-day crater floor game drive — Big Five year-round',
                'Maasai village cultural visit',
                'Private 4x4 with professional safari guide',
            ],
            'itinerary' => [
                ['title' => 'Day 1 — Arusha to the Crater Rim',   'description' => 'Picked up from your Arusha hotel or Kilimanjaro Airport in the morning. Scenic drive through the highlands and Karatu farmland to your lodge perched on the crater rim with sunset views and dinner.'],
                ['title' => 'Day 2 — Full-Day Crater Game Drive', 'description' => 'Descend at dawn for a full-day exploration of the crater floor — lions, rhinos, elephants, flamingos. Picnic lunch on the floor. Return to your rim lodge for dinner.'],
                ['title' => 'Day 3 — Cultural Visit & Return',    'description' => 'Visit a Maasai village to learn local traditions, then transfer back to Arusha or Kilimanjaro Airport in the afternoon for your onward journey.'],
            ],
        ],

        [
            'name' => 'Ruaha Wilderness Adventure',
            'slug' => 'ruaha-wilderness-adventure',
            'description' => "Raw, rugged, and rich with untamed beauty — Ruaha National Park is Tanzania's best-kept secret. Encounter big cats, towering baobabs, and elephant herds beside the lifeline of the Great Ruaha River, in solitude rarely found in the busier reserves.\n\nFour days of fly-in safari, walking expeditions, and bush dinners under starlit skies.",
            'duration_days' => 4,
            'duration_nights' => 3,
            'destinations' => ['Ruaha National Park'],
            'categories' => ['First-Time Safaris', 'Luxury Safaris'],
            'cover' => 'destination-ruaha.webp',
            'gallery' => ['gallery-zebra.webp', 'gallery-elephants.webp', 'gallery-luxury-lodge.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 2100, 'size' => 8, 'color' => '#10b981'],
                ['name' => 'Luxury',   'price' => 3100, 'size' => 4, 'color' => '#f59e0b'],
            ],
            'inclusions' => [
                'Return flights from Dar es Salaam',
                'Three nights luxury bush camp',
                'Full-board meals & drinks',
                'All park & conservation fees',
                'Game drives & walking safaris',
                'Sundowner & starlight dinner experiences',
            ],
            'exclusions' => [
                'International flights',
                'Tanzania visa fees',
                'Premium wines & champagne',
                'Tips & gratuities',
            ],
            'highlights' => [
                'Tanzania\'s largest national park, in solitude',
                'Big-cat country: 10% of Africa\'s lions',
                'Walking safari with an armed ranger',
                'Starlight bush dinner and sundowners',
            ],
            'itinerary' => [
                ['title' => 'Day 1 — Fly into Ruaha',               'description' => 'Connect from Dar es Salaam (or Zanzibar via Dar) on a scheduled light aircraft to Ruaha airstrip. Afternoon arrival game drive to camp. Welcome dinner and sundowners.'],
                ['title' => 'Day 2 — Game Drives & Big Cats',       'description' => 'Full-day drives along the Great Ruaha River searching for prides of lions and leopard, with picnic lunch in the bush. Overnight at camp.'],
                ['title' => 'Day 3 — Walking Safari & Bush Lunch',  'description' => 'Morning walking safari with an armed ranger followed by lunch in the bush. Afternoon game drive and a starlight dinner under the open sky.'],
                ['title' => 'Day 4 — Final Drive & Departure',      'description' => 'Sunrise game drive and breakfast at camp before your scheduled flight back to Dar es Salaam for onward connections.'],
            ],
        ],

        [
            'name' => 'Honeymoon Zanzibar Retreat',
            'slug' => 'honeymoon-zanzibar-retreat',
            'description' => "A romantic escape crafted for two — secluded beach villas, private candlelit dinners on the sand, and sunset dhow cruises across the Indian Ocean. Zanzibar pairs UNESCO-listed culture in Stone Town with some of Africa's most beautiful beaches at Nungwi and Mnemba.\n\nFive days designed for intimacy, gentle adventure, and absolute relaxation.",
            'duration_days' => 5,
            'duration_nights' => 4,
            'destinations' => ['Stone Town', 'Nungwi Beach', 'Mnemba Atoll'],
            'categories' => ['Honeymoon Safaris'],
            'cover' => 'category-honeymoon.webp',
            'gallery' => ['gallery-nungwi.webp', 'gallery-mnemba.webp', 'gallery-spice-farms.webp', 'destination-zanzibar.webp'],
            'groups' => [
                ['name' => 'Standard', 'price' => 2800, 'size' => 2, 'color' => '#ec4899'],
                ['name' => 'Luxury',   'price' => 4200, 'size' => 2, 'color' => '#f59e0b'],
            ],
            'inclusions' => [
                'Airport transfers & in-island transport',
                'Four nights beachfront villa stay',
                'Daily breakfast & one romantic dinner',
                'Guided Stone Town & spice farm tour',
                'Sunset dhow cruise with refreshments',
                'Half-day Mnemba Atoll snorkeling',
            ],
            'exclusions' => [
                'International flights',
                'Tanzania visa fees',
                'Spa treatments & premium drinks',
                'Tips & gratuities',
            ],
            'highlights' => [
                'Stone Town boutique stay and UNESCO walking tour',
                'Spice farm sensory afternoon',
                'Beachfront villa nights at Nungwi',
                'Mnemba Atoll snorkelling',
                'Sunset dhow sail for two',
            ],
            'itinerary' => [
                ['title' => 'Day 1 — Arrival in Stone Town',    'description' => 'Land at Zanzibar International Airport. Met on arrival and transferred to your boutique stay in Stone Town, with a welcome dinner overlooking the harbor.'],
                ['title' => 'Day 2 — Spice & Culture Tour',     'description' => 'Guided walking tour of Stone Town followed by a sensory afternoon at a working spice farm. Evening at leisure in town.'],
                ['title' => 'Day 3 — Transfer to Nungwi',       'description' => 'Morning drive north to your beachfront villa at Nungwi. Sunset cocktails on the sand and dinner at the resort.'],
                ['title' => 'Day 4 — Mnemba Atoll Snorkeling',  'description' => 'Boat trip to the pristine reef around Mnemba for snorkelling and a beach picnic for two. Afternoon at leisure on the beach.'],
                ['title' => 'Day 5 — Sunset Dhow & Farewell',   'description' => 'Day at leisure with an optional spa. Traditional dhow sail at sunset and an evening transfer back to Zanzibar International Airport.'],
            ],
        ],
    ];

    public function run(): void
    {
        $importer = new PublicAssetImporter($this->command);
        $importer->convertAll();

        $userId = 1;
        $tripTypeId = TripType::query()->where('name', 'Individual')->value('id')
            ?? TripType::query()->value('id');
        $tripStatusId = TripStatus::query()->where('name', 'Approved')->value('id')
            ?? TripStatus::query()->value('id');
        $tripSourceId = TripSource::query()->where('name', 'Management')->value('id')
            ?? TripSource::query()->value('id');
        $usdId = Currency::query()->where('short_name', 'USD')->value('id');
        $adultAgeGroupId = AgeGroup::query()->where('name', 'Adult')->value('id')
            ?? AgeGroup::query()->value('id');

        foreach ($this->trips as $data) {
            $fromDate = Carbon::parse(self::DEFAULT_DEPARTURE)->startOfDay();
            $toDate = $fromDate->copy()->addDays(max(0, $data['duration_days'] - 1));

            // TripObserver::created() rewrites the slug as Str::slug($name);
            // mirror that here so re-runs find the existing row instead of
            // inserting a duplicate. The data array's `slug` field is kept
            // for human readability only.
            $canonicalSlug = Str::slug($data['name']);
            $trip = Trip::firstOrNew(['slug' => $canonicalSlug]);
            $trip->name = $data['name'];
            $trip->description = $data['description'];
            $trip->from_date = $fromDate->toDateString();
            $trip->to_date = $toDate->toDateString();
            $trip->duration_days = $data['duration_days'];
            $trip->duration_nights = $data['duration_nights'];
            $trip->trip_type_id = $tripTypeId;
            $trip->trip_source_id = $tripSourceId;
            $trip->trip_status_id = $tripStatusId;
            $trip->is_published = true;
            $trip->created_by = $trip->created_by ?: $userId;
            $trip->save();

            $importer->attachCoverAndGallery($trip, $data['cover'], $data['gallery'] ?? []);

            $this->syncDestinations($trip, $data['destinations'], $userId);
            $this->syncCategories($trip, $data['categories'], $userId);
            $this->syncGroupsAndPrices($trip, $data['groups'], $fromDate, $usdId, $adultAgeGroupId, $userId);
            $this->syncItinerary($trip, $data['destinations'], $data['itinerary'], $data['duration_days'], $userId);
            $this->syncInclusionsExclusionsAsAddons($trip, $data['inclusions'], $data['exclusions'], $userId);
            $this->syncHighlights($trip, $data['highlights'] ?? [], $userId);
            $this->syncBudgets($trip, $data['groups'], $usdId, $userId);

            $trip->forceFill([
                'trip_status_id' => $tripStatusId,
                'is_published' => true,
                'publish_remarks' => 'Published by public-site trip seeder.',
                'updated_by' => $userId,
            ])->save();
        }
    }

    private function syncDestinations(Trip $trip, array $names, int $userId): void
    {
        foreach ($names as $name) {
            $destinationId = Destination::query()->where('name', $name)->value('id');
            if (!$destinationId) {
                continue;
            }
            $pivot = TripDestination::firstOrCreate(
                ['trip_id' => $trip->id, 'destination_id' => $destinationId],
                ['created_by' => $userId, 'is_active' => true]
            );

            // Backfill: trip_destinations.is_active defaults to 0; ensure
            // pivots stay enabled on re-runs even if older rows pre-date the
            // default-on convention.
            if (!$pivot->is_active) {
                $pivot->is_active = true;
                $pivot->save();
            }
        }

        if (!in_array('Zanzibar Island', $names, true)) {
            $legacyZanzibarId = Destination::query()->where('name', 'Zanzibar Island')->value('id');
            if ($legacyZanzibarId) {
                TripDestination::query()
                    ->where('trip_id', $trip->id)
                    ->where('destination_id', $legacyZanzibarId)
                    ->update(['is_active' => false]);
            }
        }
    }

    private function syncCategories(Trip $trip, array $names, int $userId): void
    {
        foreach ($names as $name) {
            $categoryId = Category::query()->where('name', $name)->value('id');
            if (!$categoryId) {
                continue;
            }
            TripCategory::firstOrCreate(
                ['trip_id' => $trip->id, 'category_id' => $categoryId],
                ['created_by' => $userId]
            );
        }
    }

    private function syncGroupsAndPrices(
        Trip $trip,
        array $groups,
        Carbon $departureDate,
        ?int $currencyId,
        ?int $ageGroupId,
        int $userId
    ): void {
        foreach ($groups as $i => $group) {
            $tripGroup = TripGroup::firstOrNew([
                'trip_id' => $trip->id,
                'group' => $group['name'],
            ]);

            $tripGroup->size = $group['size'];
            $tripGroup->color = $group['color'];
            $tripGroup->departure_date = $departureDate->toDateString();
            $tripGroup->days = (string) $trip->duration_days;
            $tripGroup->description = $group['name'] . ' departure for ' . $trip->name;
            $tripGroup->is_active = true;
            $tripGroup->created_by = $tripGroup->created_by ?: $userId;
            $tripGroup->save();

            if ($currencyId && $ageGroupId) {
                $tripPrice = TripPrice::firstOrNew([
                    'trip_id' => $trip->id,
                    'trip_group_id' => $tripGroup->id,
                    'age_group_id' => $ageGroupId,
                    'currency_id' => $currencyId,
                ]);

                $tripPrice->price = $group['price'];
                $tripPrice->is_discounted = false;
                $tripPrice->from_date = $departureDate->toDateString();
                $tripPrice->to_date = $departureDate->copy()->addYear()->toDateString();
                $tripPrice->is_active = true;
                $tripPrice->created_by = $tripPrice->created_by ?: $userId;
                $tripPrice->save();
            }
        }
    }

    /**
     * Build a fully-formed itinerary with destination, accommodation,
     * meal plan, nights and a representative day activity per row.
     *
     * Heuristics:
     *  - Days are rotated across the trip's destinations; longer trips with
     *    multiple destinations distribute days evenly across them.
     *  - The accommodation used for each day is the primary accommodation
     *    of that day's destination (PublicSiteAccommodationSeeder ensures
     *    every destination has at least one lodge/camp).
     *  - Meals: arrival day has lunch+dinner, departure day has breakfast
     *    only, all other days are full-board.
     *  - Each day gets one TripDayActivity matched to its destination.
     */
    private function syncItinerary(Trip $trip, array $destinationNames, array $itinerary, int $durationDays, int $userId): void
    {
        $destinationIds = collect($destinationNames)
            ->map(fn ($name) => Destination::query()->where('name', $name)->value('id'))
            ->filter()
            ->values();

        if ($destinationIds->isEmpty()) {
            return;
        }

        $accommodations = Accommodation::query()
            ->whereIn('primary_destination_id', $destinationIds)
            ->get()
            ->keyBy('primary_destination_id');

        // Hard cap: an itinerary must never have more entries than the trip
        // duration. If the data has more rows, only the first N are seeded.
        // (Authors should consolidate intra-day events into one rich
        // description instead of spawning extra rows.)
        $maxRows = max(1, $durationDays);
        $itinerary = array_slice($itinerary, 0, $maxRows);
        $dayCount = count($itinerary);

        // Wipe ALL pre-existing TripDay rows for this trip and re-create
        // from scratch. This is the only way to guarantee a clean shape
        // when the seeder's day count or titles change between runs — e.g.
        // Mikumi went from 8 hourly time-slot rows → 1 consolidated day.
        // forceDelete bypasses SoftDeletes so the rows are physically
        // removed (and trip_day_activities cascade-deletes too).
        TripDay::query()
            ->where('trip_id', $trip->id)
            ->forceDelete();

        foreach ($itinerary as $i => $day) {
            $dayNumber = $i + 1;
            $isFirstDay = $dayNumber === 1;
            $isLastDay = $dayNumber === $dayCount;

            // Spread the day across the trip's destinations as evenly as possible.
            $destIndex = (int) floor(($i * $destinationIds->count()) / max($dayCount, 1));
            $dayDestinationId = $destinationIds[$destIndex] ?? $destinationIds->first();

            $accommodation = $accommodations->get($dayDestinationId);
            $isOvernight = !$isLastDay;
            $stayTypeName = $accommodation && $accommodation->stayType ? $accommodation->stayType->name : null;

            // Meal heuristics:
            //  - 1-day trip   → lunch only (typical bush / packed lunch day)
            //  - first day    → arrival, lunch + dinner
            //  - last day     → departure, breakfast only
            //  - middle day   → full board (breakfast + lunch + dinner)
            if ($dayCount === 1) {
                $breakfast = false; $lunch = true;  $dinner = false;
            } else {
                $breakfast = !$isFirstDay;
                $lunch     = true;
                $dinner    = !$isLastDay;
            }

            $tripDay = TripDay::updateOrCreate(
                ['trip_id' => $trip->id, 'day_number' => $dayNumber, 'title' => $day['title']],
                [
                    'destination_id' => $dayDestinationId,
                    'accommodation_id' => $isOvernight ? $accommodation?->id : null,
                    'accommodation_name' => $isOvernight ? $accommodation?->name : null,
                    'stay_type' => $isOvernight ? $stayTypeName : null,
                    'nights' => $isOvernight ? 1 : 0,
                    'breakfast' => $breakfast,
                    'lunch' => $lunch,
                    'dinner' => $dinner,
                    'description' => $day['description'],
                    'sort_order' => $dayNumber,
                    'created_by' => $userId,
                ]
            );

            $this->syncDayActivity($tripDay, $dayDestinationId, $day, $userId);
        }
    }

    /**
     * Pick a representative activity for the day based on the destination
     * type and attach a single TripDayActivity row.
     */
    private function syncDayActivity(TripDay $tripDay, ?int $destinationId, array $day, int $userId): void
    {
        $destinationName = $destinationId
            ? Destination::query()->where('id', $destinationId)->value('name')
            : null;

        $activityName = match (true) {
            $destinationName && str_contains($destinationName, 'Stone Town') => 'City Walking Tour',
            $destinationName && str_contains($destinationName, 'Zanzibar') => 'Beach Relaxation',
            $destinationName && str_contains($destinationName, 'Beach') => 'Beach Relaxation',
            $destinationName && str_contains($destinationName, 'Atoll') => 'Beach Relaxation',
            default => 'Wildlife Safari',
        };

        $activityId = Activity::query()->where('name', $activityName)->value('id')
            ?? Activity::query()->value('id');

        if (!$activityId) {
            return;
        }

        TripDayActivity::firstOrCreate(
            ['trip_day_id' => $tripDay->id, 'title' => $day['title']],
            [
                'activity_id' => $activityId,
                'description' => $day['description'],
                'is_optional' => false,
                'sort_order' => 1,
                'created_by' => $userId,
            ]
        );
    }

    /**
     * Convert this trip's inclusions and exclusions into proper Addon rows
     * (with `is_include` flag) and link them via trip_addons. This is the
     * shape the QuoteBuilderService expects: it reads `trip->addons->addon`
     * and uses `is_include` to split included / excluded terms on the
     * quotation. Free-text "Included: …" rows on trip_points are obsolete
     * and replaced with this.
     */
    private function syncInclusionsExclusionsAsAddons(Trip $trip, array $inclusions, array $exclusions, int $userId): void
    {
        $sync = function (array $lines, bool $isInclude) use ($trip, $userId): void {
            foreach ($lines as $line) {
                $name = trim((string) $line);
                if ($name === '') {
                    continue;
                }
                $addon = Addon::firstOrNew(['name' => $name]);
                $addon->is_include = $isInclude;
                $addon->created_by = $addon->created_by ?: $userId;
                $addon->save();

                $link = TripAddon::firstOrCreate(
                    ['trip_id' => $trip->id, 'addon_id' => $addon->id],
                    ['created_by' => $userId, 'is_active' => true]
                );
                if (!$link->is_active) {
                    $link->is_active = true;
                    $link->save();
                }
            }
        };

        $sync($inclusions, true);
        $sync($exclusions, false);
    }

    /**
     * Trip highlights — short selling-point bullets shown on the trip page
     * (e.g. "Mara River crossing window", "Sundowner drinks on the plains").
     * Stored on trip_points which historically were misused for
     * inclusion/exclusion strings; that data now lives on addons.
     *
     * Re-runs sync: any pre-existing trip_points titled with the legacy
     * "Included: " / "Not Included: " prefix are removed so the highlights
     * tab on the trip show page only displays real bullets.
     */
    private function syncHighlights(Trip $trip, array $highlights, int $userId): void
    {
        TripPoint::query()
            ->where('trip_id', $trip->id)
            ->where(function ($q) {
                $q->where('title', 'like', 'Included: %')
                    ->orWhere('title', 'like', 'Not Included: %');
            })
            ->forceDelete();

        foreach ($highlights as $index => $line) {
            $title = trim((string) $line);
            if ($title === '') {
                continue;
            }
            TripPoint::firstOrCreate(
                ['trip_id' => $trip->id, 'title' => $title],
                [
                    'description' => $title,
                    'created_by' => $userId,
                ]
            );
        }
    }

    /**
     * Produce a budget matrix per trip × season × service class × USD,
     * derived from the trip's published group prices. Multipliers approximate
     * realistic spread between Economy / Semi Luxury / Luxury and high vs
     * low season — operators can tune these in the Budgets admin.
     */
    private function syncBudgets(Trip $trip, array $groups, ?int $currencyId, int $userId): void
    {
        if (!$currencyId) {
            return;
        }

        $standardPrice = collect($groups)->firstWhere('name', 'Standard')['price']
            ?? collect($groups)->first()['price']
            ?? 0;

        if ($standardPrice <= 0) {
            return;
        }

        $seasons = Season::query()->get();
        $classes = ServiceClass::query()->get();
        $groupSizes = [2, 4, 6];

        Budget::query()
            ->where('trip_id', $trip->id)
            ->whereNotIn('quantity', $groupSizes)
            ->update(['is_active' => false]);

        foreach ($seasons as $season) {
            $seasonMultiplier = str_contains(strtolower($season->name), 'high') ? 1.0 : 0.85;

            foreach ($classes as $class) {
                $classMultiplier = match (strtolower($class->name)) {
                    'economy' => 0.75,
                    'semi luxury' => 1.0,
                    'luxury' => 1.4,
                    default => 1.0,
                };

                foreach ($groupSizes as $groupSize) {
                    $groupMultiplier = match ($groupSize) {
                        2 => 1.0,
                        4 => 0.92,
                        6 => 0.86,
                    };
                    $price = round((float) $standardPrice * $seasonMultiplier * $classMultiplier * $groupMultiplier, 2);

                    Budget::updateOrCreate(
                        [
                            'trip_id' => $trip->id,
                            'season_id' => $season->id,
                            'service_class_id' => $class->id,
                            'currency_id' => $currencyId,
                            'quantity' => $groupSize,
                        ],
                        [
                            'price' => $price,
                            'is_active' => true,
                            'created_by' => $userId,
                        ]
                    );
                }
            }
        }
    }

}
