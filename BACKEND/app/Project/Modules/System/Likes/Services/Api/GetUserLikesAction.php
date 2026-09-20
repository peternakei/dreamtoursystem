<?php

namespace App\Project\Modules\System\Likes\Services\Api;

use App\Project\Modules\System\Likes\Like;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;

class GetUserLikesAction
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

            // Get user likes with likeable data
            $likes = Like::with(['likeable'])
                ->where('tourist_id', $tourist->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($like) {
                    $likeableData = null;

                    if ($like->likeable) {
                        $likeableData = [
                            'id' => $like->likeable->id,
                            'uuid' => $like->likeable->uuid,
                            'name' => $like->likeable->name ?? $like->likeable->title,
                            'description' => $like->likeable->description ?? null,
                            'type' => class_basename($like->likeable),
                            'slug' => $like->likeable->slug ?? null,
                        ];

                        // Add specific data for trips
                        if ($like->likeable_type === 'App\Project\Modules\System\Trips\Trip') {
                            try {
                                if (method_exists($like->likeable, 'prices') && $like->likeable->prices && $like->likeable->prices->count() > 0) {
                                    $likeableData['base_price'] = $like->likeable->prices->first()?->price ?? 0;
                                    $likeableData['currency'] = $like->likeable->prices->first()?->currency->code ?? 'USD';
                                }
                                $likeableData['duration'] = $like->likeable->duration ?? null;
                                $likeableData['difficulty'] = $like->likeable->difficulty ?? null;
                                $likeableData['group_size'] = $like->likeable->group_size ?? null;
                            } catch (\Exception $e) {
                                // Skip trip-specific data if there's an error
                            }
                        }

                        // Add specific data for destinations
                        if ($like->likeable_type === 'App\Project\Modules\System\Destinations\Destination') {
                            try {
                                $likeableData['country'] = $like->likeable->country ?? null;
                                $likeableData['region'] = $like->likeable->region ?? null;
                            } catch (\Exception $e) {
                                // Skip destination-specific data if there's an error
                            }
                        }
                    }

                    return [
                        'id' => $like->id,
                        'uuid' => $like->uuid,
                        'tourist_id' => $like->tourist_id,
                        'likeable_type' => $like->likeable_type,
                        'likeable_id' => $like->likeable_id,
                        'likeable_uuid' => $like->likeable_uuid,
                        'created_at' => $like->created_at,
                        'likeable' => $likeableData
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => [
                    'likes' => $likes
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve likes: ' . $e->getMessage()
            ], 500);
        }
    }
}
