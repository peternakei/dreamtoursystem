<?php

namespace App\Project\Modules\System\Likes\Services\Api;

use App\Project\Modules\System\Likes\Like;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;

class CheckLikeStatusAction
{
    public function handle(Request $request, $type)
    {
        try {
            $user = $request->user();
            $tourist = $user->userProfile;

            $uuid = $request->query('uuid');

            if (!$uuid) {
                return response()->json([
                    'status' => false,
                    'message' => 'UUID is required'
                ], 400);
            }

            // Find the item
            $likeable = null;
            $likeableType = null;

            switch ($type) {
                case 'trip':
                    $likeable = Trip::where('uuid', $uuid)->first();
                    $likeableType = Trip::class;
                    break;
                case 'destination':
                    $likeable = Destination::where('uuid', $uuid)->first();
                    $likeableType = Destination::class;
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid type'
                    ], 400);
            }

            if (!$likeable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check if liked
            $like = Like::where([
                'tourist_id' => $tourist->id,
                'likeable_type' => $likeableType,
                'likeable_id' => $likeable->id,
            ])->first();

            return response()->json([
                'status' => true,
                'data' => [
                    'is_liked' => $like ? true : false,
                    'like_id' => $like ? $like->uuid : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to check like status: ' . $e->getMessage()
            ], 500);
        }
    }
}
