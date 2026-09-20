<?php

namespace App\Project\Modules\System\Dashboard\Services\Api;

use App\Project\Modules\System\Likes\Like;
use App\Project\Modules\System\Ratings\Rating;
use App\Project\Modules\System\Cart\CartItem;
use App\Project\Modules\System\Bookings\Booking;
use Illuminate\Http\Request;
use App\Project\Modules\System\Tourists\Tourist;

class GetUserDashboardAction
{
    public function handle(Request $request)
    {
        try {
            $user = $request->user();
            // Get the authenticated tourist with relationships
            $tourist = Tourist::with(['gender', 'country'])
                ->where('id', $user->profile_id)
                ->first();

            if (!$tourist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tourist profile not found'
                ], 404);
            }

                    // Get user profile information
        $userProfile = [
            'id' => $tourist->id,
            'uuid' => $tourist->uuid,
            'name' => $tourist->name,
            'email' => $tourist->email,
            'phone' => $tourist->phone,
            'tourist_number' => $tourist->tourist_number,
            'gender' => $tourist->gender ? $tourist->gender->name : null,
            'country' => $tourist->country ? $tourist->country->name : null,
            'address' => $tourist->address,
            'is_active' => $tourist->is_active,
            'created_at' => $tourist->created_at,
            'updated_at' => $tourist->updated_at
        ];

            // Get statistics
            $statistics = [
                'total_likes' => Like::where('tourist_id', $tourist->id)->count(),
                'total_ratings' => Rating::where('tourist_id', $tourist->id)->count(),
                'total_cart_items' => CartItem::where('tourist_id', $tourist->id)->count(),
                'total_bookings' => Booking::where('tourist_id', $tourist->id)->count(),
                'active_bookings' => Booking::where('tourist_id', $tourist->id)
                    ->whereHas('status', function($query) {
                        $query->whereIn('name', ['confirmed', 'active', 'in_progress']);
                    })->count(),
                'completed_bookings' => Booking::where('tourist_id', $tourist->id)
                    ->whereHas('status', function($query) {
                        $query->where('name', 'completed');
                    })->count()
            ];

            // Get user likes with likeable data
            $likes = Like::with(['likeable'])
                ->where('tourist_id', $tourist->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($like) {
                    $likeableData = null;

                    if ($like->likeable) {
                        $likeableData = [
                            'id' => $like->likeable->id,
                            'uuid' => $like->likeable->uuid,
                            'name' => $like->likeable->name ?? $like->likeable->title,
                            'description' => $like->likeable->description ?? null,
                            'type' => class_basename($like->likeable),
                            'slug' => $like->likeable->slug ?? null,
                        ];

                        // Add specific data for trips
                        if ($like->likeable_type === 'App\Project\Modules\System\Trips\Trip') {
                            // Load trip-specific data safely
                            try {
                                if (method_exists($like->likeable, 'banners') && $like->likeable->banners) {
                                    $likeableData['banner'] = $like->likeable->banners->first()?->file_path ?? null;
                                }
                                if (method_exists($like->likeable, 'prices') && $like->likeable->prices) {
                                    $likeableData['base_price'] = $like->likeable->prices->first()?->price ?? 0;
                                    $likeableData['currency'] = $like->likeable->prices->first()?->currency->code ?? 'USD';
                                }
                                $likeableData['duration'] = $like->likeable->duration ?? null;
                                $likeableData['difficulty'] = $like->likeable->difficulty ?? null;
                                $likeableData['group_size'] = $like->likeable->group_size ?? null;

                                // Add trip destinations
                                if (method_exists($like->likeable, 'destinations') && $like->likeable->destinations) {
                                    $likeableData['destinations'] = $like->likeable->destinations->map(function ($tripDest) {
                                        return [
                                            'name' => $tripDest->destination->name ?? null,
                                            'location' => $tripDest->destination->location->name ?? null,
                                            'description' => $tripDest->destination->description ?? null
                                        ];
                                    });
                                }
                            } catch (\Exception $e) {
                                // Skip trip-specific data if there's an error
                            }
                        }

                        // Add specific data for destinations
                        if ($like->likeable_type === 'App\Project\Modules\System\Destinations\Destination') {
                            try {
                                $likeableData['location'] = $like->likeable->location->name ?? null;
                                $likeableData['country'] = $like->likeable->country ?? null;
                                $likeableData['region'] = $like->likeable->region ?? null;
                            } catch (\Exception $e) {
                                // Skip destination-specific data if there's an error
                            }
                        }
                    }

                    return [
                        'id' => $like->id,
                        'uuid' => $like->uuid,
                        'tourist_id' => $like->tourist_id,
                        'likeable_type' => $like->likeable_type,
                        'likeable_id' => $like->likeable_id,
                        'likeable_uuid' => $like->likeable_uuid,
                        'created_at' => $like->created_at,
                        'likeable' => $likeableData
                    ];
                });

            // Get user ratings with rateable data
            $ratings = Rating::with(['rateable'])
                ->where('tourist_id', $tourist->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($rating) {
                    $rateableData = null;

                    if ($rating->rateable) {
                        $rateableData = [
                            'id' => $rating->rateable->id,
                            'uuid' => $rating->rateable->uuid,
                            'name' => $rating->rateable->name ?? $rating->rateable->title,
                            'description' => $rating->rateable->description ?? null,
                            'type' => class_basename($rating->rateable),
                            'slug' => $rating->rateable->slug ?? null,
                        ];

                        // Add specific data for trips
                        if ($rating->rateable_type === 'App\Project\Modules\System\Trips\Trip') {
                            try {
                                if (method_exists($rating->rateable, 'banners') && $rating->rateable->banners && $rating->rateable->banners->count() > 0) {
                                    $rateableData['banner'] = $rating->rateable->banners->first()?->file_path ?? null;
                                }
                                if (method_exists($rating->rateable, 'prices') && $rating->rateable->prices && $rating->rateable->prices->count() > 0) {
                                    $rateableData['base_price'] = $rating->rateable->prices->first()?->price ?? 0;
                                    $rateableData['currency'] = $rating->rateable->prices->first()?->currency->code ?? 'USD';
                                }
                                $rateableData['duration'] = $rating->rateable->duration ?? null;
                                $rateableData['difficulty'] = $rating->rateable->difficulty ?? null;

                                // Add trip destinations for location info
                                if (method_exists($rating->rateable, 'destinations') && $rating->rateable->destinations && $rating->rateable->destinations->count() > 0) {
                                    $firstDestination = $rating->rateable->destinations->first();
                                    if ($firstDestination && $firstDestination->destination && $firstDestination->destination->location) {
                                        $rateableData['location'] = $firstDestination->destination->location->name ?? 'Tanzania Mainland';
                                    }
                                }

                                // Set default location if none found
                                if (empty($rateableData['location'])) {
                                    $rateableData['location'] = 'Tanzania Mainland';
                                }
                            } catch (\Exception $e) {
                                // Skip trip-specific data if there's an error
                                $rateableData['location'] = 'Tanzania Mainland';
                            }
                        }

                        // Add specific data for destinations
                        if ($rating->rateable_type === 'App\Project\Modules\System\Destinations\Destination') {
                            try {
                                if (method_exists($rating->rateable, 'location') && $rating->rateable->location) {
                                    $rateableData['location'] = $rating->rateable->location->name ?? 'Tanzania Mainland';
                                }
                                $rateableData['country'] = $rating->rateable->country ?? null;

                                // Set default location if none found
                                if (empty($rateableData['location'])) {
                                    $rateableData['location'] = 'Tanzania Mainland';
                                }
                            } catch (\Exception $e) {
                                // Skip destination-specific data if there's an error
                                $rateableData['location'] = 'Tanzania Mainland';
                            }
                        }
                    }

                    return [
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
                        'rateable' => $rateableData
                    ];
                });

            // Get user cart items with enhanced data
            $cartItems = CartItem::where('tourist_id', $tourist->id)
                ->with(['cartable'])
                ->get()
                ->map(function ($item) {
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
                            if (method_exists($item->cartable, 'banners') && $item->cartable->banners && $item->cartable->banners->count() > 0) {
                                $cartableData['banner'] = $item->cartable->banners->first()?->file_path ?? null;
                            }
                            if (method_exists($item->cartable, 'prices') && $item->cartable->prices && $item->cartable->prices->count() > 0) {
                                $cartableData['base_price'] = $item->cartable->prices->first()?->price ?? 0;
                                $cartableData['currency'] = $item->cartable->prices->first()?->currency->code ?? 'USD';
                            }
                            $cartableData['duration'] = $item->cartable->duration ?? null;
                            $cartableData['difficulty'] = $item->cartable->difficulty ?? null;

                            // Add trip destinations for location info
                            if (method_exists($item->cartable, 'destinations') && $item->cartable->destinations && $item->cartable->destinations->count() > 0) {
                                $cartableData['destinations'] = $item->cartable->destinations->map(function ($tripDest) {
                                    return [
                                        'name' => $tripDest->destination->name ?? null,
                                        'location' => $tripDest->destination->location->name ?? null
                                    ];
                                });

                                // Set location from first destination
                                $firstDestination = $item->cartable->destinations->first();
                                if ($firstDestination && $firstDestination->destination && $firstDestination->destination->location) {
                                    $cartableData['location'] = $firstDestination->destination->location->name ?? 'Tanzania Mainland';
                                }
                            }

                            // Set default location if none found
                            if (empty($cartableData['location'])) {
                                $cartableData['location'] = 'Tanzania Mainland';
                            }
                        } catch (\Exception $e) {
                            // Skip trip-specific data if there's an error
                            $cartableData['location'] = 'Tanzania Mainland';
                        }
                    }

                    // Add specific data for destinations
                    if ($item->cartable_type === 'App\Project\Modules\System\Destinations\Destination') {
                        try {
                            if (method_exists($item->cartable, 'location') && $item->cartable->location) {
                                $cartableData['location'] = $item->cartable->location->name ?? 'Tanzania Mainland';
                            }
                            $cartableData['country'] = $item->cartable->country ?? null;

                            // Set default location if none found
                            if (empty($cartableData['location'])) {
                                $cartableData['location'] = 'Tanzania Mainland';
                            }
                        } catch (\Exception $e) {
                            // Skip destination-specific data if there's an error
                            $cartableData['location'] = 'Tanzania Mainland';
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

            // Get user bookings with comprehensive trip data
            $bookings = Booking::with([
                'trip',
                'trip.destinations.destination.location',
                'status',
                'currency',
                'payments'
            ])
                ->where('tourist_id', $tourist->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    $tripDetails = null;
                    if ($booking->trip) {
                        $tripDetails = [
                            'id' => $booking->trip->id,
                            'uuid' => $booking->trip->uuid,
                            'name' => $booking->trip->name,
                            'description' => $booking->trip->description,
                            'duration' => $booking->trip->duration,
                            'difficulty' => $booking->trip->difficulty,
                            'group_size' => $booking->trip->group_size,
                            'destinations' => $booking->trip->destinations->map(function ($tripDest) {
                                return [
                                    'name' => $tripDest->destination->name ?? null,
                                    'location' => $tripDest->destination->location->name ?? null,
                                    'description' => $tripDest->destination->description ?? null
                                ];
                            })->filter()->values(),
                            'accommodation' => $booking->trip->accommodation_type ?? 'Luxury Lodge',
                            'transport' => $booking->trip->transport_type ?? '4x4 Safari Vehicle',
                        ];

                        // Safely add banner and price data
                        try {
                            if (method_exists($booking->trip, 'banners') && $booking->trip->banners) {
                                $tripDetails['banner'] = $booking->trip->banners->first()?->file_path ?? null;
                            }
                            if (method_exists($booking->trip, 'prices') && $booking->trip->prices) {
                                $tripDetails['base_price'] = $booking->trip->prices->first()?->price ?? 0;
                                $tripDetails['currency'] = $booking->trip->prices->first()?->currency->code ?? 'USD';
                            }
                        } catch (\Exception $e) {
                            // Skip banner/price data if there's an error
                        }
                    }

                    $paymentDetails = null;
                    if ($booking->payments && $booking->payments->count() > 0) {
                        $payment = $booking->payments->first();
                        $paymentDetails = [
                            'transaction_id' => $payment->reference_number ?? null,
                            'payment_date' => $payment->receipt_date ?? null,
                            'amount_paid' => $payment->amount ?? 0,
                            'currency' => $payment->currency->code ?? 'USD',
                            'payment_method' => $payment->payment_method ?? 'credit_card',
                            'status' => $payment->status ?? 'completed'
                        ];
                    }

                    $statusDetails = [
                        'name' => $booking->status->name ?? 'pending',
                        'description' => $booking->status->description ?? null,
                        'color' => $this->getStatusColor($booking->status->name ?? 'pending')
                    ];

                    return [
                        'id' => $booking->id,
                        'uuid' => $booking->uuid,
                        'tourist_id' => $booking->tourist_id,
                        'trip_id' => $booking->trip_id,
                        'trip_uuid' => $booking->trip->uuid ?? null,
                        'trip_name' => $booking->trip->name ?? null,
                        'guest_count' => $booking->guest_count,
                        'start_date' => $booking->trip->from_date ?? null,
                        'end_date' => $booking->trip->to_date ?? null,
                        'total_amount' => $booking->total_amount,
                        'currency' => $booking->currency->code ?? 'USD',
                        'status' => $statusDetails,
                        'payment_status' => $this->getPaymentStatus($booking),
                        'payment_method' => $paymentDetails['payment_method'] ?? 'credit_card',
                        'payment_details' => $paymentDetails,
                        'trip_details' => $tripDetails,
                        'special_requests' => $booking->special_requests ?? null,
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at
                    ];
                });

            // Calculate cart total
            $cartTotal = $cartItems->sum(function($item) {
                if ($item['cartable'] && isset($item['cartable']['base_price'])) {
                    return $item['cartable']['base_price'] * $item['quantity'];
                }
                return 0;
            });

            // Prepare final response
            $dashboardData = [
                'user_profile' => $userProfile,
                'statistics' => $statistics,
                'cart_summary' => [
                    'total_items' => $cartItems->count(),
                    'total_amount' => $cartTotal,
                    'currency' => 'USD'
                ],
                'recent_activity' => [
                    'recent_likes' => $likes->take(5),
                    'recent_ratings' => $ratings->take(5),
                    'recent_bookings' => $bookings->take(5)
                ],
                'likes' => $likes,
                'ratings' => $ratings,
                'cart_items' => $cartItems,
                'bookings' => $bookings
            ];

            return response()->json([
                'status' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => $dashboardData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPaymentStatus($booking)
    {
        if ($booking->total_amount <= 0) {
            return 'paid';
        }

        $paidAmount = $booking->payments ? $booking->payments->sum('amount') : 0;

        if ($paidAmount >= $booking->total_amount) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partial';
        } else {
            return 'pending';
        }
    }

    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#FFA500',
            'confirmed' => '#008000',
            'active' => '#0000FF',
            'in_progress' => '#800080',
            'completed' => '#008000',
            'cancelled' => '#FF0000',
            'refunded' => '#808080'
        ];

        return $colors[$status] ?? '#808080';
    }
}
