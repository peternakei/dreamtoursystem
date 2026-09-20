<?php

namespace App\Project\Modules\System\Trips\Seeders;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationCategory;
use App\Project\Modules\System\Trips\Category;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripCategory;
use Database\Seeders\Support\PublicAssetImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Idempotent — safe to re-run.
 *
 * Seeds the five must-have categories shown on the marketing site, then:
 *  - re-points any destination_categories / trip_categories rows that
 *    referenced now-removed categories to the default ("First-Time Safaris"),
 *  - removes legacy categories that are no longer one of the five,
 *  - backfills destinations / trips that have NO category at all by
 *    linking them to the default category,
 *  - attaches the matching marketing-site cover image to each category.
 */
class CategorySeeder extends Seeder
{
    /**
     * The five must-have categories. Add more via the Categories admin.
     * Order matters — the first entry is treated as the default.
     * `cover` refers to a WebP filename in public/storage/uploads/
     * (produced by PublicAssetImporter::convertAll()).
     */
    protected array $defaults = [
        [
            'name' => 'First-Time Safaris',
            'color' => '#76520e',
            'description' => "Begin your journey with expertly guided safaris across Tanzania's most iconic landscapes.",
            'cover' => 'category-first-time-safaris.webp',
        ],
        [
            'name' => 'Honeymoon Safaris',
            'color' => '#ec4899',
            'description' => 'Romantic escapes with private stays and unforgettable moments.',
            'cover' => 'category-honeymoon.webp',
        ],
        [
            'name' => 'Safari & Zanzibar',
            'color' => '#06b6d4',
            'description' => 'Experience the perfect balance of wildlife and island serenity.',
            'cover' => 'category-safari-zanzibar.webp',
        ],
        [
            'name' => 'Luxury Safaris',
            'color' => '#f59e0b',
            'description' => 'Premium lodges, private guides, and seamless travel experiences.',
            'cover' => 'category-luxury.webp',
        ],
        [
            'name' => 'Family Safaris',
            'color' => '#10b981',
            'description' => 'Family-friendly itineraries with kid-safe lodges, gentle pacing, and shared adventures.',
            'cover' => 'category-family.webp',
        ],
    ];

    public function run(): void
    {
        $importer = new PublicAssetImporter($this->command);
        $importer->convertAll();

        $defaultName = $this->defaults[0]['name'];

        $defaultCategory = Category::firstOrCreate(
            ['name' => $defaultName],
            [
                'color' => $this->defaults[0]['color'],
                'description' => $this->defaults[0]['description'],
                'sort_order' => 0,
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // Upsert each must-have category and attach its cover image
        foreach ($this->defaults as $i => $data) {
            $cat = Category::firstOrNew(['name' => $data['name']]);
            $cat->color = $data['color'];
            $cat->description = $data['description'];
            $cat->sort_order = $i;
            $cat->is_active = true;
            $cat->created_by = $cat->created_by ?: 1;
            $cat->save();

            if (!empty($data['cover'])) {
                $importer->attach($cat, $data['cover'], title: $cat->name);
            }
        }

        $defaultIds = Category::whereIn('name', array_column($this->defaults, 'name'))->pluck('id')->all();

        // Re-point any pivot rows that reference now-legacy categories to the default
        $legacyCategoryIds = Category::whereNotIn('id', $defaultIds)->pluck('id')->all();
        if (!empty($legacyCategoryIds)) {
            DestinationCategory::whereIn('category_id', $legacyCategoryIds)
                ->update(['category_id' => $defaultCategory->id]);
            TripCategory::whereIn('category_id', $legacyCategoryIds)
                ->update(['category_id' => $defaultCategory->id]);

            // Now safe to drop the legacy categories
            Category::whereIn('id', $legacyCategoryIds)->delete();
        }

        // Backfill destinations with no category
        $destinationsMissing = Destination::whereDoesntHave('categories')->pluck('id');
        foreach ($destinationsMissing as $destinationId) {
            DestinationCategory::firstOrCreate(
                ['destination_id' => $destinationId, 'category_id' => $defaultCategory->id],
                ['created_by' => 1]
            );
        }

        // Backfill trips with no category
        $tripsMissing = Trip::whereDoesntHave('categories')->pluck('id');
        foreach ($tripsMissing as $tripId) {
            TripCategory::firstOrCreate(
                ['trip_id' => $tripId, 'category_id' => $defaultCategory->id],
                ['created_by' => 1]
            );
        }

        // De-duplicate any rows that may have ended up identical after re-pointing
        $this->dedupePivot('destination_categories', ['destination_id', 'category_id']);
        $this->dedupePivot('trip_categories', ['trip_id', 'category_id']);
    }

    protected function dedupePivot(string $table, array $columns): void
    {
        $select = array_merge(
            $columns,
            [DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as cnt')]
        );

        $duplicates = DB::table($table)
            ->select($select)
            ->groupBy($columns)
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $row) {
            $query = DB::table($table)->where('id', '!=', $row->keep_id);
            foreach ($columns as $col) {
                $query->where($col, $row->{$col});
            }
            $query->delete();
        }
    }
}
