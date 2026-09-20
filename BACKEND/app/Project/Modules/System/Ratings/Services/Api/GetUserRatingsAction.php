<?php

namespace App\Project\Modules\System\Ratings\Services\Api;

use App\Project\Modules\System\Ratings\Rating;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;

class GetUserRatingsAction
{
    public function handle(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $tourist = $user->userProfile;

            // Get user ratings with rateable data
            $ratings = Rating::with(['rateable'])
                ->where('tourist_id', $tourist->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($rating) {
                    $rateableData = null;

                    if ($rating->rateable) {
                        $rateableData = [
                            'id' => $rating->rateable->id,
                            'uuid' => $rating->rateable->uuid,
                            'name' => $rating->rateable->name ?? $rating->rateable->title,
                            'description' => $rating->rateable->description ?? null,
                            'type' => class_basename($rating->rateable),
                            'slug' => $rating->rateable->slug ?? null,
                        ];

                        // Add specific data for trips
                        if ($rating->rateable_type === 'App\Project\Modules\System\Trips\Trip') {
                            try {
                                if (method_exists($rating->rateable, 'prices') && $rating->rateable->prices && $rating->rateable->prices->count() > 0) {
                                    $rateableData['base_price'] = $rating->rateable->prices->first()?->price ?? 0;
                                    $rateableData['currency'] = $rating->rateable->prices->first()?->currency->code ?? 'USD';
                                }
                                $rateableData['duration'] = $rating->rateable->duration ?? null;
                                $rateableData['difficulty'] = $rating->rateable->difficulty ?? null;
                            } catch (\Exception $e) {
                                // Skip trip-specific data if there's an error
                            }
                        }

                        // Add specific data for destinations
                        if ($rating->rateable_type === 'App\Project\Modules\System\Destinations\Destination') {
                            try {
                                $rateableData['country'] = $rating->rateable->country ?? null;
                            } catch (\Exception $e) {
                                // Skip destination-specific data if there's an error
                            }
                        }
                    }

                    return [
                        'id' => $rating->id,
                        'uuid' => $rating->uuid,
                        'tourist_id' => $rating->tourist_id,
                        'rateable_type' => $rating->rateable_type,
                        'rateable_id' => $rating->rateable_id,
                        'rateable_uuid' => $rating->rateable_uuid,
                        'rating' => $rating->rating,
                        'comment' => $rating->comment,
                        'created_at' => $rating->created_at,
                        'updated_at' => $rating->updated_at,
                        'rateable' => $rateableData
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => [
                    'ratings' => $ratings
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve ratings: ' . $e->getMessage()
            ], 500);
        }
    }
}
