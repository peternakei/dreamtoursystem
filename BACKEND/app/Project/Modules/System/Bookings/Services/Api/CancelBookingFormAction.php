<?php

namespace App\Project\Modules\System\Bookings\Services\Api;

use App\Project\Modules\System\Bookings\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelBookingFormAction
{
    public function handle(Request $request)
    {
        $user = $request->user();

        DB::beginTransaction();

        $booking = Booking::where(['uuid' => $request->uuid, 'tourist_id' => $user->profile_id])->first();
        if (!$booking) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Booking not found!'
            ]);
        }

        //cancel
        $cancel = $booking->update([
            'booking_status_id' => 4,
            'comments' => htmlspecialchars($request->remarks)
        ]);

        if (!$cancel) {
            DB::rollBack();
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Failed to cancel booking!'
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Booking cancelled successfully!',
            'data' => [
                'booking' => [
                    'id' => $booking->id,
                    'uuid' => $booking->uuid,
                    'booking_number' => $booking->booking_number
                ]
            ]
        ]);
    }
}
