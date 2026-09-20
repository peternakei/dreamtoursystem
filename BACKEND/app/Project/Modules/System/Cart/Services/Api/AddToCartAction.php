<?php

namespace App\Project\Modules\System\Cart\Services\Api;

use App\Project\Modules\System\Cart\CartItem;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddToCartAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $tourist = $user->userProfile;

            // Find the item to add to cart
            $cartable = null;
            $cartableType = null;

            switch ($request->cartable_type) {
                case 'trip':
                    $cartable = Trip::where('uuid', $request->cartable_uuid)->first();
                    $cartableType = Trip::class;
                    break;
                case 'destination':
                    $cartable = Destination::where('uuid', $request->cartable_uuid)->first();
                    $cartableType = Destination::class;
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid cartable type'
                    ], 400);
            }

            if (!$cartable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check if item already exists in cart
            $existingCartItem = CartItem::where([
                'tourist_id' => $tourist->id,
                'cartable_type' => $cartableType,
                'cartable_id' => $cartable->id,
            ])->first();

            if ($existingCartItem) {
                // Update existing cart item
                $existingCartItem->update([
                    'quantity' => $request->quantity ?? $existingCartItem->quantity + 1,
                    'start_date' => $request->start_date ?? $existingCartItem->start_date,
                    'end_date' => $request->end_date ?? $existingCartItem->end_date,
                ]);

                $cartItem = $existingCartItem;
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'tourist_id' => $tourist->id,
                    'cartable_type' => $cartableType,
                    'cartable_id' => $cartable->id,
                    'cartable_uuid' => $request->cartable_uuid,
                    'quantity' => $request->quantity ?? 1,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Item added to cart successfully',
                'data' => [
                    'id' => $cartItem->id,
                    'uuid' => $cartItem->uuid,
                    'tourist_id' => $cartItem->tourist_id,
                    'cartable_type' => $cartItem->cartable_type,
                    'cartable_id' => $cartItem->cartable_id,
                    'cartable_uuid' => $cartItem->cartable_uuid,
                    'quantity' => $cartItem->quantity,
                    'start_date' => $cartItem->start_date,
                    'end_date' => $cartItem->end_date,
                    'created_at' => $cartItem->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add item to cart: ' . $e->getMessage()
            ], 500);
        }
    }
}
