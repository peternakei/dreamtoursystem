<?php

namespace App\Project\Modules\System\Accommodations\Seeders;

use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Accommodations\StayType;
use App\Project\Modules\System\Destinations\Destination;
use Database\Seeders\Support\PublicAssetImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Synthesises a realistic accommodation library tied to each destination
 * seeded by PublicSiteDestinationSeeder. The marketing site does not
 * surface accommodations directly, but the quote builder & proposal PDF do
 * — so we seed plausible lodges/camps that let the operator demo a full
 * quotation end-to-end against any of the public-site destinations.
 *
 * Idempotent — upserts by name.
 */
class PublicSiteAccommodationSeeder extends Seeder
{
    /**
     * Each entry pairs a synth lodge/camp with:
     *  - the destination name it sits in (Destination.name)
     *  - the stay type label (StayType.name from StayTypeSeeder)
     *  - an optional cover image filename (WebP in public/storage/uploads/);
     *    when absent we re-use the destination's own cover for visual
     *    continuity in the PDF preview.
     */
    protected array $accommodations = [
        // Serengeti -------------------------------------------------------
        [
            'name' => 'Serengeti Migration Camp',
            'destination' => 'Serengeti National Park',
            'stay_type' => 'Mobile Camp',
            'description' => 'Classic mobile tented camp that follows the migration corridor — exclusive, low-impact, and perfectly positioned for the river crossings.',
            'location_text' => 'Central Serengeti',
            'cover' => 'accommodation-serengeti-migration-camp.webp',
        ],
        [
            'name' => 'Serengeti Plains Lodge',
            'destination' => 'Serengeti National Park',
            'stay_type' => 'Lodge',
            'description' => 'Permanent lodge with panoramic views over the seronera plains; pool, spa, and family suites.',
            'location_text' => 'Seronera region',
            'cover' => null,
        ],

        // Ngorongoro ------------------------------------------------------
        [
            'name' => 'Ngorongoro Crater Lodge',
            'destination' => 'Ngorongoro Crater',
            'stay_type' => 'Lodge',
            'description' => 'Iconic lodge perched on the crater rim with sweeping views over the caldera floor — the destination luxury anchor.',
            'location_text' => 'Ngorongoro Crater rim',
            'cover' => 'accommodation-ngorongoro-crater-lodge.webp',
        ],
        [
            'name' => 'Ngorongoro Highlands Camp',
            'destination' => 'Ngorongoro Crater',
            'stay_type' => 'Tented Camp',
            'description' => 'Boutique tented camp tucked into the highland forest above the crater, perfect for mid-range and adventurous travellers.',
            'location_text' => 'Karatu / crater highlands',
            'cover' => null,
        ],

        // Tarangire -------------------------------------------------------
        [
            'name' => 'Tarangire Treetops',
            'destination' => 'Tarangire National Park',
            'stay_type' => 'Lodge',
            'description' => 'Elevated tree-house style suites overlooking ancient baobabs and the Tarangire plains.',
            'location_text' => 'Tarangire ecosystem',
            'cover' => 'accommodation-tarangire-treetops.webp',
        ],
        [
            'name' => 'Tarangire River Camp',
            'destination' => 'Tarangire National Park',
            'stay_type' => 'Tented Camp',
            'description' => 'Permanent tented camp on a private concession beside the Tarangire River, ideal for elephant viewing.',
            'location_text' => 'Tarangire river corridor',
            'cover' => null,
        ],

        // Mikumi ----------------------------------------------------------
        [
            'name' => 'Mikumi Wildlife Camp',
            'destination' => 'Mikumi National Park',
            'stay_type' => 'Tented Camp',
            'description' => 'Comfortable bush camp ideally placed for early-morning game drives across the open plains of Mikumi.',
            'location_text' => 'Mikumi park gate',
            'cover' => null,
        ],

        // Selous / Nyerere ------------------------------------------------
        [
            'name' => 'Rufiji River Camp',
            'destination' => 'Nyerere / Selous Game Reserve',
            'stay_type' => 'Bush Camp',
            'description' => 'Rustic riverside camp with private decks overlooking the Rufiji — the base for boat safaris and walking expeditions.',
            'location_text' => 'Rufiji River, Nyerere',
            'cover' => null,
        ],

        // Ruaha -----------------------------------------------------------
        [
            'name' => 'Ruaha Bush Camp',
            'destination' => 'Ruaha National Park',
            'stay_type' => 'Bush Camp',
            'description' => 'Remote bush camp on the banks of the Great Ruaha River — the launchpad for walking safaris and big-cat tracking.',
            'location_text' => 'Mwagusi sand river area',
            'cover' => null,
        ],

        // Zanzibar --------------------------------------------------------
        [
            'name' => 'Nungwi Beach Villas',
            'destination' => 'Nungwi Beach',
            'stay_type' => 'Beach Resort',
            'description' => 'Private beachfront villas at Nungwi with infinity pools and sunset bars looking out over the Indian Ocean.',
            'location_text' => 'Nungwi, north Zanzibar',
            'cover' => 'gallery-nungwi.webp',
        ],
        [
            'name' => 'Mnemba Atoll Lodge',
            'destination' => 'Mnemba Atoll',
            'stay_type' => 'Beach Resort',
            'description' => 'Exclusive lodge facing the Mnemba reef — outstanding snorkelling and diving directly from the beach.',
            'location_text' => 'Mnemba atoll, east coast',
            'cover' => 'gallery-mnemba.webp',
        ],
        [
            'name' => 'Stone Town Boutique Hotel',
            'destination' => 'Stone Town',
            'stay_type' => 'Hotel',
            'description' => 'Restored merchant house in the heart of UNESCO-listed Stone Town with rooftop dining and easy access to the spice market.',
            'location_text' => 'Stone Town, Zanzibar',
            'cover' => null,
        ],
    ];

    public function run(): void
    {
        $importer = new PublicAssetImporter($this->command);
        $importer->convertAll();

        $userId = 1;
        $defaultStayTypeId = StayType::query()->value('id');

        foreach ($this->accommodations as $i => $data) {
            $destination = Destination::query()->where('name', $data['destination'])->first();
            if (!$destination) {
                continue;
            }

            $stayTypeId = StayType::query()->where('name', $data['stay_type'])->value('id')
                ?? $defaultStayTypeId;

            $accommodation = Accommodation::firstOrCreate(
                ['name' => $data['name']],
                [
                    'stay_type_id' => $stayTypeId,
                    'primary_destination_id' => $destination->id,
                    'description' => $data['description'],
                    'location_text' => $data['location_text'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                    'created_by' => $userId,
                ]
            );

            $accommodation->fill([
                'stay_type_id' => $stayTypeId,
                'primary_destination_id' => $destination->id,
                'description' => $data['description'],
                'location_text' => $data['location_text'],
                'sort_order' => $i + 1,
                'is_active' => true,
            ])->save();

            // Cover: prefer accommodation-specific image, fall back to destination cover.
            $cover = $data['cover'] ?: $this->destinationCoverFor($destination->name);
            if ($cover) {
                $importer->attach($accommodation, $cover, title: $accommodation->name);
            }

            // Always sync the accommodation_destination pivot so the primary
            // destination is also reachable through the belongsToMany relation.
            $accommodation->destinations()->syncWithoutDetaching([
                $destination->id => ['is_primary' => true, 'sort_order' => 0],
            ]);

            $legacyZanzibarId = Destination::query()->where('name', 'Zanzibar Island')->value('id');
            if ($legacyZanzibarId && $destination->name !== 'Zanzibar Island') {
                DB::table('accommodation_destination')
                    ->where('accommodation_id', $accommodation->id)
                    ->where('destination_id', $legacyZanzibarId)
                    ->delete();
            }
        }
    }

    private function destinationCoverFor(string $destinationName): ?string
    {
        return match ($destinationName) {
            'Serengeti National Park'        => 'destination-serengeti.webp',
            'Tarangire National Park'         => 'destination-tarangire.webp',
            'Mikumi National Park'            => 'destination-mikumi.webp',
            'Ngorongoro Crater'               => 'destination-ngorongoro.webp',
            'Nyerere / Selous Game Reserve'   => 'destination-selous.webp',
            'Ruaha National Park'             => 'destination-ruaha.webp',
            'Stone Town'                      => 'destination-zanzibar.webp',
            'Nungwi Beach'                    => 'gallery-nungwi.webp',
            'Kendwa Beach'                    => 'gallery-kendwa.webp',
            'Mnemba Atoll'                    => 'gallery-mnemba.webp',
            'Jozani Forest'                   => 'gallery-jozani.webp',
            'Zanzibar Spice Farms'            => 'gallery-spice-farms.webp',
            'Prison Island'                   => 'gallery-prison-island.webp',
            'Nakupenda Sandbank'              => 'gallery-nakupenda.webp',
            default                           => null,
        };
    }
}
