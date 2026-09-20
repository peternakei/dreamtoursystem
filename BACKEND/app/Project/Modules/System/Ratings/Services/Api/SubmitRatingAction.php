<?php

namespace App\Project\Modules\System\Ratings\Services\Api;

use App\Project\Modules\System\Ratings\Rating;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubmitRatingAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $tourist = $user->userProfile;

            // Find the item to rate
            $rateable = null;
            $rateableType = null;

            switch ($request->rateable_type) {
                case 'trip':
                    $rateable = Trip::where('uuid', $request->rateable_uuid)->first();
                    $rateableType = Trip::class;
                    break;
                case 'destination':
                    $rateable = Destination::where('uuid', $request->rateable_uuid)->first();
                    $rateableType = Destination::class;
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid rateable type'
                    ], 400);
            }

            if (!$rateable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check if user already rated this item
            $existingRating = Rating::where([
                'tourist_id' => $tourist->id,
                'rateable_type' => $rateableType,
                'rateable_id' => $rateable->id,
            ])->first();

            if ($existingRating) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already rated this item. Use update endpoint to modify your rating.'
                ], 400);
            }

            // Create new rating
            $rating = Rating::create([
                'tourist_id' => $tourist->id,
                'rateable_type' => $rateableType,
                'rateable_id' => $rateable->id,
                'rateable_uuid' => $request->rateable_uuid,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Rating submitted successfully',
                'data' => [
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
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to submit rating: ' . $e->getMessage()
            ], 500);
        }
    }
}
