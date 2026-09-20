<?php

namespace App\Project\Modules\System\Cart\Services\Api;

use App\Project\Modules\System\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RemoveFromCartAction
{
    public function handle($cartItemUuid, Request $request)
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

            $cartItem = CartItem::where([
                'uuid' => $cartItemUuid,
                'tourist_id' => $tourist->id
            ])->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $cartItem->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Item removed from cart successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from cart: ' . $e->getMessage()
            ], 500);
        }
    }
}
