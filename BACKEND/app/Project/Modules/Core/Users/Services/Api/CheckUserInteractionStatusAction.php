<?php

namespace App\Project\Modules\Core\Users\Services\Api;

use App\Project\Modules\System\Likes\Like;
use App\Project\Modules\System\Ratings\Rating;
use App\Project\Modules\System\Cart\CartItem;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;

class CheckUserInteractionStatusAction
{
    public function handle(Request $request)
    {
        try {
            $user = $request->user();
            $tourist = $user->userProfile;

            $type = $request->query('type'); // trip or destination
            $uuid = $request->query('uuid');

            if (!$type || !$uuid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Type and UUID are required'
                ], 400);
            }

            // Find the item
            $item = null;
            $itemType = null;

            switch ($type) {
                case 'trip':
                    $item = Trip::where('uuid', $uuid)->first();
                    $itemType = Trip::class;
                    break;
                case 'destination':
                    $item = Destination::where('uuid', $uuid)->first();
                    $itemType = Destination::class;
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid type'
                    ], 400);
            }

            if (!$item) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check like status
            $like = Like::where([
                'tourist_id' => $tourist->id,
                'likeable_type' => $itemType,
                'likeable_id' => $item->id,
            ])->first();

            // Check rating status
            $rating = Rating::where([
                'tourist_id' => $tourist->id,
                'rateable_type' => $itemType,
                'rateable_id' => $item->id,
            ])->first();

            // Check cart status
            $cartItem = CartItem::where([
                'tourist_id' => $tourist->id,
                'cartable_type' => $itemType,
                'cartable_id' => $item->id,
            ])->first();

            return response()->json([
                'status' => true,
                'data' => [
                    'is_liked' => $like ? true : false,
                    'user_rating' => $rating ? [
                        'rating' => $rating->rating,
                        'comment' => $rating->comment
                    ] : null,
                    'is_in_cart' => $cartItem ? true : false,
                    'cart_quantity' => $cartItem ? $cartItem->quantity : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to check interaction status: ' . $e->getMessage()
            ], 500);
        }
    }
}
