<?php

namespace App\Project\Modules\System\Cart\Services\Api;

use App\Project\Modules\System\Cart\CartItem;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetUserCartAction
{
    public function handle(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $tourist = $user->userProfile;

            // Get user cart items with cartable data
            $cartItems = CartItem::with(['cartable'])
                ->where('tourist_id', $tourist->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $cartableData = null;

                    if ($item->cartable) {
                        $cartableData = [
                            'id' => $item->cartable->id,
                            'uuid' => $item->cartable->uuid,
                            'name' => $item->cartable->name ?? $item->cartable->title,
                            'description' => $item->cartable->description ?? null,
                            'type' => class_basename($item->cartable),
                            'slug' => $item->cartable->slug ?? null,
                        ];

                        // Add specific data for trips
                        if ($item->cartable_type === 'App\Project\Modules\System\Trips\Trip') {
                            try {
                                if (method_exists($item->cartable, 'prices') && $item->cartable->prices && $item->cartable->prices->count() > 0) {
                                    $cartableData['base_price'] = $item->cartable->prices->first()?->price ?? 0;
                                    $cartableData['currency'] = $item->cartable->prices->first()?->currency->code ?? 'USD';
                                }
                                $cartableData['duration'] = $item->cartable->duration ?? null;
                                $cartableData['difficulty'] = $item->cartable->difficulty ?? null;
                            } catch (\Exception $e) {
                                // Skip trip-specific data if there's an error
                            }
                        }

                        // Add specific data for destinations
                        if ($item->cartable_type === 'App\Project\Modules\System\Destinations\Destination') {
                            try {
                                $cartableData['country'] = $item->cartable->country ?? null;
                            } catch (\Exception $e) {
                                // Skip destination-specific data if there's an error
                            }
                        }
                    }

                    return [
                        'id' => $item->id,
                        'uuid' => $item->uuid,
                        'tourist_id' => $item->tourist_id,
                        'cartable_type' => $item->cartable_type,
                        'cartable_id' => $item->cartable_id,
                        'cartable_uuid' => $item->cartable_uuid,
                        'quantity' => $item->quantity,
                        'start_date' => $item->start_date,
                        'end_date' => $item->end_date,
                        'created_at' => $item->created_at,
                        'cartable' => $cartableData
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => [
                    'cart_items' => $cartItems
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve cart: ' . $e->getMessage()
            ], 500);
        }
    }
}
