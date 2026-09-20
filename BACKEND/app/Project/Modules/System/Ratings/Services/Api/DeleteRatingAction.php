<?php

namespace App\Project\Modules\System\Ratings\Services\Api;

use App\Project\Modules\System\Ratings\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteRatingAction
{
    public function handle($ratingUuid, Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

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

            $rating->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Rating deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete rating: ' . $e->getMessage()
            ], 500);
        }
    }
}
