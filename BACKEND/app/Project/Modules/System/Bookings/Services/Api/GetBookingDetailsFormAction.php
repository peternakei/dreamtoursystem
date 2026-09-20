<?php

namespace App\Project\Modules\System\Bookings\Services\Api;

use App\Project\Modules\System\Bookings\Booking;
use Illuminate\Http\Request;

class GetBookingDetailsFormAction
{
    public function handle(Request $request)
    {
        $user = $request->user();

        $booking = Booking::with([
            'tourist' => function ($query) {
                $query->select('id', 'name', 'email');
            },
            'bookingType' => function ($query) {
                $query->select('id', 'name');
            },
            'currency' => function ($query) {
                $query->select('id', 'name');
            },
            'trip' => function ($query) {
                $query->select('id', 'name', 'trip_code');
            },
            'status' => function ($query) {
                $query->select('id', 'name');
            },
            'tripGroup' => function ($query) {
                $query->with(['camps' => function () {}]);
            }
        ])->where(['tourist_id' => $user->profile_id, 'uuid' => $request->uuid])->get();

        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Booking fetched successfully',
            'data' => [
                'booking' => $booking
            ]
        ]);
    }
}
