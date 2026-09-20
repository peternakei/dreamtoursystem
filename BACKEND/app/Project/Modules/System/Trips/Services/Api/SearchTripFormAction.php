<?php

namespace App\Project\Modules\System\Trips\Services\Api;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripDestination;
use App\Project\Modules\System\Trips\TripGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchTripFormAction
{
    public function handle(Request $request)
    {
        $categoryUuids = $this->normalizeFilterValues($request->input('category_uuid'));
        $categoryIds = $this->normalizeFilterValues($request->input('category_id'));

        $destinationIds = Destination::query()
            ->where('location_id', $request->location)
            ->pluck('id');

        $tripIds = TripDestination::query()
            ->whereIn('destination_id', $destinationIds)
            ->pluck('trip_id');

        $trips = Trip::query()
            ->whereIn('id', $tripIds)
            ->where('is_published', true)
            ->whereHas('tripStatus', function ($query) {
                $query->where('name', 'Approved');
            })
            ->when($categoryUuids->isNotEmpty(), function ($query) use ($categoryUuids) {
                $query->whereHas('categories.category', function ($categoryQuery) use ($categoryUuids) {
                    $categoryQuery->whereIn('uuid', $categoryUuids->all());
                });
            })
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds) {
                $query->whereHas('categories.category', function ($categoryQuery) use ($categoryIds) {
                    $categoryQuery->whereIn('id', $categoryIds->all());
                });
            })
            ->with([
                'tripType' => function ($query) {
                    $query->select('id', 'name');
                },
                'tripSource' => function ($query) {
                    $query->select('id', 'name');
                },
                'tripStatus' => function ($query) {
                    $query->select('id', 'name');
                },
                'categories.category' => function ($query) {
                    $query->select('id', 'uuid', 'name');
                }
            ])
            ->orderByDesc('id')
            ->get();

        $data = [];

        foreach ($trips as $trip) {

            $image = $trip->banners()->latest()->first();

            $data[] = [
                'id' => $trip->id,
                'uuid' => $trip->uuid,
                'trip_code' => $trip->trip_code,
                'name' => $trip->name,
                'slug' => $trip->slug,
                'description' => $trip->description,
                'from_date' => $trip->from_date,
                'to_date' => $trip->to_date,
                'duration_days' => $trip->effectiveDurationDays(),
                'duration_nights' => $trip->effectiveDurationNights(),
                'last_booking_date' => $trip->last_booking_date,
                'last_payment_date' => $trip->last_payment_date,
                'trip_type' => $trip->tripType,
                'trip_source' => $trip->tripSource,
                'trip_status' => $trip->tripStatus,
                'is_published' => $trip->is_published ? True : False,
                'created_at' => $trip->created_at,
                'destination_count' => TripDestination::where('trip_id', $trip->id)->count('id'),
                'base_price' => $this->resolveBasePrice($trip),
                'banner' => $image?->url ?? '',
                'groups' => TripGroup::select('id', 'uuid', 'group')->where('trip_id', $trip->id)->get() ?? [],
                'categories' => $trip->categories
                    ->filter(fn ($tripCategory) => $tripCategory->category)
                    ->map(function ($tripCategory) {
                        return [
                            'id' => $tripCategory->category->id,
                            'uuid' => $tripCategory->category->uuid,
                            'name' => $tripCategory->category->name,
                        ];
                    })
                    ->values(),
            ];
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Trips searched successfully',
            'data' => [
                'trips' => $data,
                'filters' => [
                    'location' => $request->location,
                    'category_uuid' => $categoryUuids->values()->all(),
                    'category_id' => $categoryIds->values()->all(),
                ]
            ]
        ]);
    }

    protected function resolveBasePrice(Trip $trip): float|int
    {
        return $trip->budgets()
            ->orderByDesc('price')
            ->value('price') ?? 0;
    }

    protected function normalizeFilterValues(mixed $input): Collection
    {
        if ($input === null || $input === '') {
            return collect();
        }

        $values = is_array($input) ? $input : explode(',', (string) $input);

        return collect($values)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values();
    }
}
