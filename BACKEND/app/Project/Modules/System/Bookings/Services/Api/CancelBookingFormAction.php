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

        $service = \App\Project\Modules\System\Inquiries\InquiryServiceDetail::where('booking_id', $booking->id)->first();
        if ($service) {
            if ($user->profile !== 'Tourist' || !$user->is_active || $service->customer_user_id !== $user->id) { DB::rollBack(); abort(403); }
            $service->update(['operations' => array_merge($service->operations ?? [], [
                'cancellation_requested' => true, 'cancellation_reason' => $request->remarks,
                'cancellation_requested_at' => now()->toIso8601String(),
            ])]);
            DB::commit();
            return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Cancellation requested; staff will review the agreed service terms.',
                'data' => ['booking' => ['id' => $booking->id, 'uuid' => $booking->uuid, 'booking_number' => $booking->booking_number]]]);
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
