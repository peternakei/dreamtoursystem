<?php

namespace App\Project\Modules\System\Destinations\Seeders;

use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\Core\Locations\Location;
use App\Project\Modules\Core\Regions\Region;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationActivity;
use App\Project\Modules\System\Destinations\DestinationCategory;
use App\Project\Modules\System\Destinations\DestinationFact;
use App\Project\Modules\System\Trips\Category;
use Database\Seeders\Support\PublicAssetImporter;
use Illuminate\Database\Seeder;

/**
 * Seeds the destinations shown on the public Seren Blue Safaris site.
 *
 * Mirrors lib/mock-data/destinations.ts from the Next.js frontend with the
 * addition of individual Zanzibar island destinations for the
 * honeymoon and Safari & Zanzibar trip catalog.
 *
 * Idempotent — re-runs upsert by name.
 */
class PublicSiteDestinationSeeder extends Seeder
{
    /**
     * Each entry pairs frontend destination metadata with the WebP cover
     * filename produced by PublicAssetImporter::convertAll().
     *
     *  - `region` matches a Region.name from public/tanzania/regions.json
     *  - `categories` are Category.name slugs that link via destination_categories
     *  - `gallery` files attach as additional Attachment rows after the cover
     */
    protected array $destinations = [
        [
            'name' => 'Serengeti National Park',
            'description' => "Home of the Great Migration and endless golden plains teeming with predators and prey. The Serengeti's open savannah is the most iconic safari landscape in Africa.",
            'latitude' => -2.333333,
            'longitude' => 34.833333,
            'location' => 'Tanzania Mainland',
            'region' => 'Mara',
            'cover' => 'destination-serengeti.webp',
            'gallery' => ['gallery-girrafe.webp', 'gallery-elephants.webp', 'gallery-zebra.webp'],
            'categories' => ['First-Time Safaris', 'Luxury Safaris'],
            'facts' => [
                ['fact' => 'Park size', 'sub_fact' => '14,750 km²', 'description' => 'One of the largest protected ecosystems on Earth, stretching from northern Tanzania into the Maasai Mara.'],
                ['fact' => 'Best time to visit', 'sub_fact' => 'June – October', 'description' => 'Peak dry season aligns with the Mara River crossings of the Great Migration.'],
                ['fact' => 'Big Five', 'sub_fact' => 'All year', 'description' => 'Lion, leopard, elephant, buffalo and black rhino are all resident inside the ecosystem.'],
                ['fact' => 'UNESCO status', 'sub_fact' => 'World Heritage Site since 1981', 'description' => 'Recognised for its annual migration of two million wildebeest, zebra and gazelle.'],
            ],
            'activities' => ['Wildlife Safari', 'Sightseeing', 'Cultural Workshop'],
        ],
        [
            'name' => 'Tarangire National Park',
            'description' => 'Land of giants, ancient baobabs, and vast elephant herds gathered along the Tarangire River. Tarangire pairs beautifully with the northern circuit as a softer, less-crowded opener.',
            'latitude' => -3.83,
            'longitude' => 36.0,
            'location' => 'Tanzania Mainland',
            'region' => 'Manyara',
            'cover' => 'destination-tarangire.webp',
            'gallery' => ['gallery-luxury-lodge.webp', 'gallery-elephants.webp'],
            'categories' => ['First-Time Safaris'],
            'facts' => [
                ['fact' => 'Park size', 'sub_fact' => '2,850 km²', 'description' => 'Tanzania\'s sixth-largest national park, dominated by the Tarangire River and its tributaries.'],
                ['fact' => 'Best time to visit', 'sub_fact' => 'June – November', 'description' => 'The dry season concentrates huge elephant herds around the river.'],
                ['fact' => 'Iconic flora', 'sub_fact' => 'Baobab trees', 'description' => 'Ancient baobabs over 2,000 years old dot the landscape and shape the park\'s silhouette.'],
            ],
            'activities' => ['Wildlife Safari', 'Sightseeing'],
        ],
        [
            'name' => 'Mikumi National Park',
            'description' => "Often called the 'Mini Serengeti', Mikumi offers an authentic safari experience with abundant wildlife and easy accessibility — perfect for a short escape from Zanzibar's coast.",
            'latitude' => -7.4,
            'longitude' => 37.0,
            'location' => 'Tanzania Mainland',
            'region' => 'Morogoro',
            'cover' => 'destination-mikumi.webp',
            'gallery' => ['gallery-elephants.webp'],
            'categories' => ['First-Time Safaris'],
            'facts' => [
                ['fact' => 'Park size', 'sub_fact' => '3,230 km²', 'description' => "Tanzania's fourth-largest national park, a key part of the Selous ecosystem corridor."],
                ['fact' => 'Best time to visit', 'sub_fact' => 'June – October', 'description' => 'The Mkata floodplain offers open game viewing through the dry months.'],
                ['fact' => 'Accessibility', 'sub_fact' => 'Day-trip from Zanzibar', 'description' => 'Short scheduled flight from Zanzibar makes Mikumi ideal for a single-day safari add-on.'],
            ],
            'activities' => ['Wildlife Safari', 'Sightseeing'],
        ],
        [
            'name' => 'Ngorongoro Crater',
            'description' => "A UNESCO World Heritage caldera with one of the highest densities of wildlife in Africa. The world's largest intact volcanic crater forms a 600-metre-deep amphitheatre teeming with the Big Five year-round.",
            'latitude' => -3.166667,
            'longitude' => 35.583333,
            'location' => 'Tanzania Mainland',
            'region' => 'Arusha',
            'cover' => 'destination-ngorongoro.webp',
            'gallery' => ['gallery-luxury-lodge.webp', 'gallery-maasai.webp'],
            'categories' => ['Luxury Safaris', 'First-Time Safaris'],
            'facts' => [
                ['fact' => 'Crater diameter', 'sub_fact' => '19 km wide', 'description' => "World's largest intact volcanic caldera, with walls up to 600m high enclosing 260km² of habitat."],
                ['fact' => 'Best time to visit', 'sub_fact' => 'Year round', 'description' => 'Permanent water and grazing keep the wildlife inside the crater across every season.'],
                ['fact' => 'UNESCO status', 'sub_fact' => 'World Heritage Site since 1979', 'description' => 'Co-listed for natural and cultural heritage, including Olduvai Gorge\'s hominid fossils.'],
                ['fact' => 'Resident species', 'sub_fact' => '25,000+ large mammals', 'description' => 'One of the densest concentrations of large mammals on the planet, including critically endangered black rhino.'],
            ],
            'activities' => ['Wildlife Safari', 'Cultural Workshop', 'Sightseeing'],
        ],
        [
            'name' => 'Nyerere / Selous Game Reserve',
            'description' => "Wild, remote, and untouched wilderness by the water. Africa's largest protected area, with hippos and crocodiles along the mighty Rufiji River and big cats on the open plains.",
            'latitude' => -8.5,
            'longitude' => 38.0,
            'location' => 'Tanzania Mainland',
            'region' => 'Morogoro',
            'cover' => 'destination-selous.webp',
            'gallery' => ['gallery-elephants.webp'],
            'categories' => ['Luxury Safaris'],
            'facts' => [
                ['fact' => 'Reserve size', 'sub_fact' => '30,893 km²', 'description' => "Africa's largest game reserve — bigger than Switzerland — with very low visitor density."],
                ['fact' => 'Best time to visit', 'sub_fact' => 'June – October', 'description' => 'Water levels drop, concentrating wildlife around the Rufiji River and oxbow lakes.'],
                ['fact' => 'Signature experience', 'sub_fact' => 'Boat & walking safaris', 'description' => 'One of the few parks where you can game-view by boat as well as on foot.'],
            ],
            'activities' => ['Wildlife Safari', 'Kayaking', 'Hiking'],
        ],
        [
            'name' => 'Ruaha National Park',
            'description' => "Raw, rugged, and rich with untamed beauty — Tanzania's largest park sees a fraction of the visitors of the northern circuit. Towering baobabs, big cats, and elephant herds along the Great Ruaha River.",
            'latitude' => -7.5,
            'longitude' => 35.0,
            'location' => 'Tanzania Mainland',
            'region' => 'Iringa',
            'cover' => 'destination-ruaha.webp',
            'gallery' => ['gallery-zebra.webp', 'gallery-elephants.webp'],
            'categories' => ['Luxury Safaris', 'First-Time Safaris'],
            'facts' => [
                ['fact' => 'Park size', 'sub_fact' => '20,226 km²', 'description' => "Tanzania's largest national park, set in the southern wilderness circuit."],
                ['fact' => 'Best time to visit', 'sub_fact' => 'May – November', 'description' => 'Dry season pulls wildlife to the Great Ruaha River; quieter than the northern parks.'],
                ['fact' => 'Big-cat density', 'sub_fact' => '10% of Africa\'s lions', 'description' => 'Ruaha is one of the most important strongholds for African lions and wild dog.'],
            ],
            'activities' => ['Wildlife Safari', 'Hiking', 'Adventure Sports'],
        ],
        [
            'name' => 'Stone Town',
            'description' => 'Zanzibar\'s UNESCO-listed old quarter, where carved wooden doors, coral-stone lanes, spice markets, rooftop restaurants, and waterfront history tell the island\'s Swahili, Arab, Indian, and European story.',
            'latitude' => -6.162222,
            'longitude' => 39.192139,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'destination-zanzibar.webp',
            'gallery' => ['gallery-spice-farms.webp', 'gallery-prison-island.webp', 'gallery-nakupenda.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'UNESCO status', 'sub_fact' => 'World Heritage Site since 2000', 'description' => 'Recognised as an outstanding example of a Swahili coastal trading town shaped by centuries of Indian Ocean exchange.'],
                ['fact' => 'Best time to visit', 'sub_fact' => 'June - October & December - February', 'description' => 'Dry-season windows make walking tours, rooftop dinners, and harbour sunsets more comfortable.'],
                ['fact' => 'Signature experience', 'sub_fact' => 'Walking tour', 'description' => 'Explore the Old Fort, Forodhani Gardens, Darajani Market, and carved-door streets with a local guide.'],
            ],
            'activities' => ['City Walking Tour', 'Food Tour', 'Cultural Workshop', 'Shopping'],
        ],
        [
            'name' => 'Nungwi Beach',
            'description' => 'A lively north-coast beach village known for clear water, white sand, dhow-building heritage, sunset cruises, swimming-friendly tides, and beachfront resorts ideal for honeymoon stays.',
            'latitude' => -5.726667,
            'longitude' => 39.298611,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-nungwi.webp',
            'gallery' => ['gallery-kendwa.webp', 'gallery-mnemba.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'Best time to visit', 'sub_fact' => 'June - October & December - February', 'description' => 'Reliable sunshine and calmer seas suit beach days, dhow cruises, and reef excursions.'],
                ['fact' => 'Coastline', 'sub_fact' => 'North Zanzibar', 'description' => 'The northern tip has some of the island\'s most swimmable tides and classic sunset views.'],
                ['fact' => 'Signature experience', 'sub_fact' => 'Sunset dhow cruise', 'description' => 'Traditional sailings leave from the beach in the late afternoon as the sun drops over the Indian Ocean.'],
            ],
            'activities' => ['Beach Relaxation', 'Food Tour', 'Shopping'],
        ],
        [
            'name' => 'Kendwa Beach',
            'description' => 'A relaxed north-west Zanzibar beach with broad white sand, calm turquoise water, memorable sunsets, and easy access to Nungwi while keeping a softer resort feel.',
            'latitude' => -5.753333,
            'longitude' => 39.289444,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-kendwa.webp',
            'gallery' => ['gallery-nungwi.webp', 'gallery-mnemba.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'Best time to visit', 'sub_fact' => 'June - October & December - February', 'description' => 'Dry months are best for beach stays, sunset dinners, and calm-water swimming.'],
                ['fact' => 'Beach style', 'sub_fact' => 'Soft sand and sunsets', 'description' => 'Kendwa is prized for its wide beach, west-facing sunset views, and easy swimming conditions.'],
                ['fact' => 'Nearby access', 'sub_fact' => 'Nungwi coast', 'description' => 'Guests can pair quiet resort time with Nungwi restaurants, dhow trips, and nightlife nearby.'],
            ],
            'activities' => ['Beach Relaxation', 'Food Tour'],
        ],
        [
            'name' => 'Mnemba Atoll',
            'description' => 'A protected reef area off Zanzibar\'s north-east coast, famous for snorkelling and diving among coral gardens, reef fish, green turtles, and clear blue water.',
            'latitude' => -5.819722,
            'longitude' => 39.381944,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-mnemba.webp',
            'gallery' => ['gallery-nungwi.webp', 'gallery-kendwa.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'Marine setting', 'sub_fact' => 'Reef and atoll', 'description' => 'Boat excursions visit the surrounding reef waters for snorkelling, diving, and marine-life viewing.'],
                ['fact' => 'Best time to visit', 'sub_fact' => 'June - October & December - February', 'description' => 'Clearer, calmer conditions usually make these months the strongest choice for water activities.'],
                ['fact' => 'Wildlife', 'sub_fact' => 'Turtles, dolphins, reef fish', 'description' => 'The area is known for rich coral life and frequent sightings of tropical marine species.'],
            ],
            'activities' => ['Beach Relaxation', 'Adventure Sports', 'Sightseeing'],
        ],
        [
            'name' => 'Jozani Forest',
            'description' => 'Zanzibar\'s best-known forest reserve, home to the endemic red colobus monkey, mangrove boardwalks, coastal forest trails, and a quieter natural counterpoint to the beaches.',
            'latitude' => -6.265833,
            'longitude' => 39.418889,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-jozani.webp',
            'gallery' => ['gallery-spice-farms.webp'],
            'categories' => ['Safari & Zanzibar', 'First-Time Safaris'],
            'facts' => [
                ['fact' => 'Signature wildlife', 'sub_fact' => 'Zanzibar red colobus', 'description' => 'The forest is the island\'s classic place to see this rare primate in its natural habitat.'],
                ['fact' => 'Landscape', 'sub_fact' => 'Forest and mangroves', 'description' => 'Trails and boardwalks move between groundwater forest, thicket, and mangrove habitat.'],
                ['fact' => 'Best time to visit', 'sub_fact' => 'Morning', 'description' => 'Cooler hours are best for guided walks and wildlife viewing before the day warms up.'],
            ],
            'activities' => ['Sightseeing', 'Hiking', 'Wildlife Safari'],
        ],
        [
            'name' => 'Zanzibar Spice Farms',
            'description' => 'Rural spice plantations where visitors taste, smell, and learn about cloves, nutmeg, cinnamon, vanilla, cardamom, tropical fruit, and Zanzibar\'s Spice Island heritage.',
            'latitude' => -6.095278,
            'longitude' => 39.241111,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-spice-farms.webp',
            'gallery' => ['destination-zanzibar.webp', 'gallery-jozani.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'Spice heritage', 'sub_fact' => 'Cloves, nutmeg, cardamom', 'description' => 'Plantation farming gave Zanzibar its Spice Island identity and remains part of everyday culture.'],
                ['fact' => 'Signature experience', 'sub_fact' => 'Guided spice tour', 'description' => 'Guests taste fresh spices and tropical fruit while learning how they grow and how local families use them.'],
                ['fact' => 'Best paired with', 'sub_fact' => 'Stone Town', 'description' => 'Spice farms sit close enough to combine with a Stone Town cultural day.'],
            ],
            'activities' => ['Food Tour', 'Cultural Workshop', 'Sightseeing'],
        ],
        [
            'name' => 'Prison Island',
            'description' => 'A small island off Stone Town known for giant tortoises, historic prison and quarantine buildings, coral-rag ruins, short boat rides, and clear-water swimming stops.',
            'latitude' => -6.119167,
            'longitude' => 39.166944,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-prison-island.webp',
            'gallery' => ['destination-zanzibar.webp', 'gallery-nakupenda.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'Also known as', 'sub_fact' => 'Changuu Island', 'description' => 'The island sits a short boat ride from Stone Town and is commonly visited as a half-day excursion.'],
                ['fact' => 'Signature wildlife', 'sub_fact' => 'Giant tortoises', 'description' => 'Its tortoise sanctuary is one of the island\'s best-known visitor experiences.'],
                ['fact' => 'Best paired with', 'sub_fact' => 'Nakupenda Sandbank', 'description' => 'Many boat trips combine the island with a sandbank picnic and swimming stop.'],
            ],
            'activities' => ['Sightseeing', 'Beach Relaxation', 'City Walking Tour'],
        ],
        [
            'name' => 'Nakupenda Sandbank',
            'description' => 'A bright white sandbank near Stone Town, loved for turquoise water, seafood picnics, swimming, snorkelling, and relaxed half-day boat excursions.',
            'latitude' => -6.151389,
            'longitude' => 39.143611,
            'location' => 'Zanzibar',
            'region' => null,
            'cover' => 'gallery-nakupenda.webp',
            'gallery' => ['gallery-prison-island.webp', 'destination-zanzibar.webp'],
            'categories' => ['Safari & Zanzibar', 'Honeymoon Safaris'],
            'facts' => [
                ['fact' => 'Setting', 'sub_fact' => 'Seasonal sandbank', 'description' => 'The sandbank is shaped by tides, so visits are timed around suitable sea conditions.'],
                ['fact' => 'Signature experience', 'sub_fact' => 'Seafood picnic', 'description' => 'Boat crews often prepare fresh seafood and fruit lunches directly on the sand.'],
                ['fact' => 'Best paired with', 'sub_fact' => 'Stone Town', 'description' => 'Its proximity makes it an easy ocean escape before or after a Stone Town stay.'],
            ],
            'activities' => ['Beach Relaxation', 'Food Tour', 'Adventure Sports'],
        ],
    ];

    public function run(): void
    {
        $importer = new PublicAssetImporter($this->command);
        $importer->convertAll();

        $userId = 1;
        Destination::query()
            ->where('name', 'Zanzibar Island')
            ->update(['is_active' => false]);

        foreach ($this->destinations as $data) {
            $locationId = Location::query()->where('name', $data['location'])->value('id')
                ?? Location::query()->value('id');

            $regionId = $data['region']
                ? Region::query()->where('name', $data['region'])->value('id')
                : null;

            $destination = Destination::firstOrCreate(
                ['name' => $data['name']],
                [
                    'location_id' => $locationId,
                    'region_id' => $regionId,
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'created_by' => $userId,
                ]
            );

            // Refresh metadata on re-runs in case copy / location / region changed.
            $destination->fill([
                'location_id' => $locationId,
                'region_id' => $regionId,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'description' => $data['description'],
                'is_active' => true,
            ])->save();

            $importer->attachCoverAndGallery(
                $destination,
                $data['cover'],
                $data['gallery'] ?? []
            );

            foreach ($data['categories'] ?? [] as $categoryName) {
                $categoryId = Category::query()->where('name', $categoryName)->value('id');
                if (!$categoryId) {
                    continue;
                }
                DestinationCategory::firstOrCreate(
                    ['destination_id' => $destination->id, 'category_id' => $categoryId],
                    ['created_by' => $userId]
                );
            }

            foreach ($data['facts'] ?? [] as $fact) {
                DestinationFact::firstOrCreate(
                    ['destination_id' => $destination->id, 'fact' => $fact['fact']],
                    [
                        'sub_fact' => $fact['sub_fact'] ?? null,
                        'description' => $fact['description'],
                        'created_by' => $userId,
                    ]
                );
            }

            foreach ($data['activities'] ?? [] as $activityName) {
                $activityId = Activity::query()->where('name', $activityName)->value('id');
                if (!$activityId) {
                    continue;
                }
                $link = DestinationActivity::firstOrCreate(
                    ['destination_id' => $destination->id, 'activity_id' => $activityId],
                    ['created_by' => $userId, 'is_active' => true]
                );
                // destination_activities.is_active defaults to 0 — keep enabled on re-runs.
                if (!$link->is_active) {
                    $link->is_active = true;
                    $link->save();
                }
            }
        }
    }
}
