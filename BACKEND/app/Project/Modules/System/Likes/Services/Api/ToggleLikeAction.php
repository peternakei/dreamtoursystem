<?php

namespace App\Project\Modules\System\Likes\Services\Api;

use App\Project\Modules\System\Likes\Like;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToggleLikeAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $tourist = $user->userProfile;

            // Find the item to like/unlike
            $likeable = null;
            $likeableType = null;

            switch ($request->likeable_type) {
                case 'trip':
                    $likeable = Trip::where('uuid', $request->likeable_uuid)->first();
                    $likeableType = Trip::class;
                    break;
                case 'destination':
                    $likeable = Destination::where('uuid', $request->likeable_uuid)->first();
                    $likeableType = Destination::class;
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid likeable type'
                    ], 400);
            }

            if (!$likeable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check if already liked
            $existingLike = Like::where([
                'tourist_id' => $tourist->id,
                'likeable_type' => $likeableType,
                'likeable_id' => $likeable->id,
            ])->first();

            if ($existingLike) {
                // Unlike - remove the like
                $existingLike->delete();
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Item unliked successfully'
                ]);
            } else {
                // Like - create new like
                $like = Like::create([
                    'tourist_id' => $tourist->id,
                    'likeable_type' => $likeableType,
                    'likeable_id' => $likeable->id,
                    'likeable_uuid' => $request->likeable_uuid,
                ]);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Item liked successfully',
                    'data' => [
                        'id' => $like->id,
                        'uuid' => $like->uuid,
                        'tourist_id' => $like->tourist_id,
                        'likeable_type' => $like->likeable_type,
                        'likeable_id' => $like->likeable_id,
                        'likeable_uuid' => $like->likeable_uuid,
                        'created_at' => $like->created_at,
                    ]
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to toggle like: ' . $e->getMessage()
            ], 500);
        }
    }
}
