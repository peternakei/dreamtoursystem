<?php

namespace App\Project\Modules\System\Ratings\Services\Api;

use App\Project\Modules\System\Ratings\Rating;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;

class CheckRatingStatusAction
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
            $rateable = null;
            $rateableType = null;

            switch ($type) {
                case 'trip':
                    $rateable = Trip::where('uuid', $uuid)->first();
                    $rateableType = Trip::class;
                    break;
                case 'destination':
                    $rateable = Destination::where('uuid', $uuid)->first();
                    $rateableType = Destination::class;
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid type'
                    ], 400);
            }

            if (!$rateable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check if rated
            $rating = Rating::where([
                'tourist_id' => $tourist->id,
                'rateable_type' => $rateableType,
                'rateable_id' => $rateable->id,
            ])->first();

            return response()->json([
                'status' => true,
                'data' => [
                    'has_rated' => $rating ? true : false,
                    'user_rating' => $rating ? [
                        'rating' => $rating->rating,
                        'comment' => $rating->comment
                    ] : null,
                    'rating_id' => $rating ? $rating->uuid : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to check rating status: ' . $e->getMessage()
            ], 500);
        }
    }
}
