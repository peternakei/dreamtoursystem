<?php

namespace App\Project\Modules\System\Cart\Services\Api;

use App\Project\Modules\System\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClearCartAction
{
    public function handle(Request $request)
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

            $deletedCount = CartItem::where('tourist_id', $tourist->id)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Cart cleared successfully',
                'data' => [
                    'deleted_items' => $deletedCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear cart: ' . $e->getMessage()
            ], 500);
        }
    }
}
