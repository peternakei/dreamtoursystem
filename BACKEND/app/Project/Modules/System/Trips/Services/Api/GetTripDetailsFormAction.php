<?php

namespace App\Project\Modules\System\Trips\Services\Api;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripDestination;
use App\Project\Modules\System\Trips\TripGroup;
use Illuminate\Http\Request;

class GetTripDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        $trip = Trip::where('uuid', $id)->firstOrFail();

        $image = $trip->banners()->latest()->first();

        $data = [
            'id' => $trip->id,
            'uuid' => $trip->uuid,
            'trip_code' => $trip->trip_code,
            'name' => $trip->name,
            'slug' => $trip->slug,
            'description' => $trip->description,
            'from_date' => $trip->from_date,
            'to_date' => $trip->to_date,
            'duration' => $trip->effectiveDurationDays() . ' days / ' . $trip->effectiveDurationNights() . ' nights',
            'last_booking_date' => $trip->last_booking_date,
            'last_payment_date' => $trip->last_payment_date,
            'trip_type' => $trip->tripType,
            'trip_source' => $trip->tripSource,
            'trip_status' => $trip->tripStatus,
            'is_published' => ($trip->is_published) ? True : False,
            'created_at' => $trip->created_at,
            'banner' => $image?->url ?? '',
            'destination_count' => TripDestination::where('trip_id', $trip->id)->count('id'),
            'base_price' => $this->resolveBasePrice($trip),
            'groups' => TripGroup::select('id', 'uuid', 'group')->where('trip_id', $trip->id)->get() ?? [],
            'trip_destination' => $trip->destinations()->with([
                'destination' => function ($query) {
                    $query->with([
                        'location' => function ($q) {
                            $q->select('id', 'name');
                        },
                        'region' => function ($q) {
                            $q->select('id', 'name');
                        },
                        'categories' => function ($q) {
                            $q->with([
                                'category' => function ($m) {
                                    $m->select('id', 'name');
                                }
                            ])->get()->map(function ($tc) {
                                return [
                                    'id' => $tc->id,
                                    'name' => $tc->name
                                ];
                            });
                        }
                    ])->get()->map(function ($dm) {
                        return [
                            'id' => $dm->id,
                            'uuid' => $dm->uuid,
                            'name' => $dm->name,
                            'description' => $dm->name,
                            'banner' => $this->resolveDestinationBanner($dm),
                            'location' => $dm->location ? [
                                'id' => $dm->location->id,
                                'name' => $dm->location->name
                            ] : null,
                            'region' => $dm->region ? [
                                'id' => $dm->region->id,
                                'name' => $dm->region->name
                            ] : null,
                            'categories' => $dm->categories
                        ];
                    });
                }
            ])->get()->map(function ($mp) {
                return [
                    'id' => $mp->id,
                    'uuid' => $mp->uuid,
                    'description' => $mp->description,
                    'is_active' => $mp->is_active == 1 ? True : False,
                    'order' => $mp->id,
                    'created_at' => $mp->created_at,
                    'day_number' => 0,
                    'destination' => $mp->destination ? [
                        'id' => $mp->destination->id,
                        'uuid' => $mp->destination->uuid,
                        'name' => $mp->destination->name,
                        'description' => $mp->destination->description,
                        'latitude' => $mp->destination->latitude,
                        'longitude' => $mp->destination->longitude,
                        'is_active' => $mp->destination->is_active == 1 ? True : False,
                        'created_at' => $mp->destination->created_at,
                        'order' => $mp->destination->id,
                        'day_number' => 0,
                        'banner' => $this->resolveDestinationBanner($mp->destination),
                        'location' => $mp->destination->location ? [
                            'id' => $mp->destination->location->id,
                            'name' => $mp->destination->location->name,
                        ] : null,
                        'region' => $mp->destination->region ? [
                            'id' => $mp->destination->region->id,
                            'name' => $mp->destination->region->name,
                        ] : null,
                        'categories' => $mp->destination->categories,
                    ] : null
                ];
            }),
            'budgets' => $trip->budgets()->where('is_active', true)->with([
                'season' => function ($query) {
                    $query->select('id', 'name');
                },
                'serviceClass' => function ($query) {
                    $query->select('id', 'name');
                },
                'currency' => function ($query) {
                    $query->select('id', 'short_name as name');
                }
            ])->get()->map(function ($budget) {
                return [
                    'id' => $budget->id,
                    'price' => $budget->price,
                    'quantity' => $budget->quantity,
                    'group_size' => (int) $budget->quantity,
                    'pricing_quantity' => (int) $budget->quantity,
                    'uuid' => $budget->uuid,
                    'created_at' => $budget->created_at,
                    'is_active' => $budget->is_active == 1 ? True : False,
                    'season' => $budget->season ? [
                        'id' => $budget->season->id,
                        'name' => $budget->season->name
                    ] : null,
                    'service_class' => $budget->serviceClass ? [
                        'id' => $budget->serviceClass->id,
                        'name' => $budget->serviceClass->name
                    ] : null,
                    'currency' => $budget->currency ? [
                        'id' => $budget->currency->id,
                        'name' => $budget->currency->name
                    ] : null,
                ];
            }),
            'trip_categories' => $trip->categories()->with([
                'category' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->get()->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'uuid' => $cat->uuid,
                    'created_at' => $cat->created_at,
                    'category' => $cat->category ? [
                        'id' => $cat->category->id,
                        'name' => $cat->category->name
                    ] : null,
                ];
            }),
            'trip_addons' => $trip->addons()->with([
                'addon' => function ($query) {
                    $query->select('id', 'name', 'is_include');
                }
            ])->get()->map(function ($add) {
                if ($add->addon->is_include == true) {
                    return [
                        'id' => $add->id,
                        'uuid' => $add->uuid,
                        'created_at' => $add->created_at,
                        'is_active' => True,
                        'category' => $add->addon ? [
                            'id' => $add->addon->id,
                            'name' => $add->addon->name
                        ] : null,
                    ];
                }
            })->filter()->values(),
            'trip_not_addons' => $trip->addons()->with([
                'addon' => function ($query) {
                    $query->select('id', 'name', 'is_include');
                }
            ])->get()->map(function ($add) {
                if ($add->addon->is_include == false) {
                    return [
                        'id' => $add->id,
                        'uuid' => $add->uuid,
                        'created_at' => $add->created_at,
                        'is_active' => True,
                        'category' => $add->addon ? [
                            'id' => $add->addon->id,
                            'name' => $add->addon->name
                        ] : null,
                    ];
                }
            })->filter()->values(),
            'trip_points' => $trip->points()->select('id', 'trip_id', 'title', 'description', 'id as order', 'uuid')->get()
        ];

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Trip retrieved successfully',
            'data' => [
                'trip' => $data
            ]
        ]);
    }

    protected function resolveBasePrice(Trip $trip): float|int
    {
        return $trip->budgets()
            ->orderByDesc('price')
            ->value('price') ?? 0;
    }

    protected function resolveDestinationBanner(Destination $destination): string
    {
        return $destination->images()->where('is_active', true)->latest()->first()?->url ?? '';
    }
}
