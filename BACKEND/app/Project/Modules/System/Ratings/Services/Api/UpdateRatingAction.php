<?php

namespace App\Project\Modules\System\Ratings\Services\Api;

use App\Project\Modules\System\Ratings\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateRatingAction
{
    public function handle(Request $request, $ratingUuid)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $tourist = $user->userProfile;

            $rating = Rating::where([
                'uuid' => $ratingUuid,
                'tourist_id' => $tourist->id
            ])->first();

            if (!$rating) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rating not found'
                ], 404);
            }

            $rating->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Rating updated successfully',
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
                'message' => 'Failed to update rating: ' . $e->getMessage()
            ], 500);
        }
    }
}
