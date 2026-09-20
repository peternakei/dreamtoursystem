<?php

namespace App\Project\Modules\System\Inquiries\Services\Api;

use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;

class GetAllInquiryFormAction
{
    public function handle(Request $request)
    {
        $user = $request->user();
        $inquiries = collect();

        if ($user) {
            // User is logged in, get inquiries by their profile_id
            $inquiries = Inquiry::whereDoesntHave('serviceDetails')->with([
                'tourist' => function ($query) {
                    $query->select('id', 'name', 'phone');
                }
            ])->where('tourist_id', $user->profile_id)->get();
        } else {
            // User is not logged in, but we need to find their inquiries
            // This would typically be called with email/phone in the request
            if ($request->has('email') || $request->has('phone')) {
                $tourist = Tourist::where('email', $request->email)
                    ->orWhere('phone', $request->phone)
                    ->first();

                if ($tourist) {
                    $inquiries = Inquiry::whereDoesntHave('serviceDetails')->with([
                        'tourist' => function ($query) {
                            $query->select('id', 'name', 'phone');
                        }
                    ])->where('tourist_id', $tourist->id)->get();
                }
            }
        }

        $formattedInquiries = $inquiries->map(function ($dat) {
            return [
                'id' => $dat->id,
                'uuid' => $dat->uuid,
                'name' => $dat->name,
                'description' => $dat->description,
                'from_date' => $dat->from_date,
                'to_date' => $dat->to_date,
                'tourist' => $dat->tourist ? [
                    'id' => $dat->tourist->id,
                    'name' => $dat->tourist->name,
                    'phone' => $dat->tourist->phone,
                ] : null,
                'created_at' => $dat->created_at
            ];
        });

        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Inquiries retrieved successfully',
            'data' => [
                'inquiries' => $formattedInquiries
            ]
        ]);
    }
}
