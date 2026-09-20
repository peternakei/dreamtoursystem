<?php

namespace App\Project\Modules\System\Cart\Services\Api;

use App\Project\Modules\System\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateCartItemAction
{
    public function handle(Request $request, $cartItemUuid)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
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

            $cartItem->update([
                'quantity' => $request->quantity ?? $cartItem->quantity,
                'start_date' => $request->start_date ?? $cartItem->start_date,
                'end_date' => $request->end_date ?? $cartItem->end_date,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Cart item updated successfully',
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
                'message' => 'Failed to update cart item: ' . $e->getMessage()
            ], 500);
        }
    }
}
