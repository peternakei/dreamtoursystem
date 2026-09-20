<?php

namespace App\Project\Modules\System\Bookings\Services\Api;

use App\Project\Modules\System\Bookings\Booking;
use Illuminate\Http\Request;

class GetAllBookingsFormAction
{
    public function handle(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::with(['tourist' => function ($query) {
            $query->select('id', 'name', 'email');
        }, 'bookingType' => function ($query) {
            $query->select('id', 'name');
        }, 'currency' => function ($query) {
            $query->select('id', 'name','short_name','symbol');
        }, 'trip' => function ($query) {
            $query->select('id', 'name', 'trip_code');
        }, 'status' => function ($query) {
            $query->select('id', 'name');
        }])->where(['tourist_id' => $user->profile_id])->get()->map(function ($bk) {
            return [
                'id' => $bk->id,
                'uuid' => $bk->uuid,
                'booking_number' => $bk->booking_number,
                'booking_date' => $bk->booking_date,
                'amount' => $bk->amount,
                'vat_amount' => $bk->vat_amount,
                'total_amount' => $bk->total_amount,
                'currency' => $bk->currency ? [
                    'id' => $bk->currency->id,
                    'name' => $bk->currency->name,
                    'short_name' => $bk->currency->short_name,
                    'symbol' => $bk->currency->symbol,
                ] : null,
                'remarks' => $bk->remarks,
                'guest_count' => $bk->guest_count,
                'tourist' => $bk->tourist ? [
                    'id' => $bk->tourist->id,
                    'name' => $bk->tourist->name,
                    'email' => $bk->tourist->email,
                ] : null,
                'bookingType' => $bk->bookingType ? [
                    'id' => $bk->bookingType->id,
                    'name' => $bk->bookingType->name,
                ] : null,
                'trip' => $bk->trip ? [
                    'id' => $bk->trip->id,
                    'name' => $bk->trip->name,
                    'code' => $bk->trip->trip_code,
                ] : null,
                'status' => $bk->status ? [
                    'id' => $bk->status->id,
                    'name' => $bk->status->name,
                ] : null,
            ];
        });

        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Bookings fetched successfully',
            'data' => [
                'bookings' => $bookings
            ]
        ]);
    }
}
