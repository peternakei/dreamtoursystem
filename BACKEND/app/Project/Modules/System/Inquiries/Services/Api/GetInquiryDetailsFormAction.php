<?php

namespace App\Project\Modules\System\Inquiries\Services\Api;

use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;

class GetInquiryDetailsFormAction
{
    public function handle(Request $request)
    {
        $user = $request->user();
        $inquiries = collect();

        if ($user) {
            // User is logged in, get inquiry by their profile_id and uuid
            $inquiries = Inquiry::whereDoesntHave('serviceDetails')->with([
                'tourist' => function ($query) {
                    $query->select('id', 'name', 'phone');
                },
                'quotations' => function ($query) {
                    $query->with([
                        'status' => function ($query) {
                            $query->select('id', 'name');
                        },
                        'currency' => function ($query) {
                            $query->select('id', 'name', 'short_name', 'symbol');
                        }
                    ]);
                }
            ])->where(['tourist_id' => $user->profile_id, 'uuid' => $request->uuid])->get();
        } else {
            // User is not logged in, but we need to find their inquiry
            // This would typically be called with email/phone in the request
            if ($request->has('email') || $request->has('phone')) {
                $tourist = Tourist::where('email', $request->email)
                    ->orWhere('phone', $request->phone)
                    ->first();

                if ($tourist) {
                    $inquiries = Inquiry::whereDoesntHave('serviceDetails')->with([
                        'tourist' => function ($query) {
                            $query->select('id', 'name', 'phone');
                        },
                        'quotations' => function ($query) {
                            $query->with([
                                'status' => function ($query) {
                                    $query->select('id', 'name');
                                },
                                'currency' => function ($query) {
                                    $query->select('id', 'name', 'short_name', 'symbol');
                                }
                            ]);
                        }
                    ])->where(['tourist_id' => $tourist->id, 'uuid' => $request->uuid])->get();
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
                'quotations' => (count($dat->quotations) > 0) ? [
                    'uuid' => $dat->quotations->uuid,
                    'id' => $dat->quotations->id,
                    'quotation_number' => $dat->quotations->quotation_number,
                    'quotation_date' => $dat->quotations->quotation_date,
                    'amount' => $dat->quotations->amount,
                    'vat_amount' => $dat->quotations->vat_amount,
                    'total_amount' => $dat->quotations->total_amount,
                    'exchange_rate' => $dat->quotations->exchange_rate,
                    'status' => $dat->quotations->status ? [
                        'id' => $dat->quotations->status->id,
                        'name' => $dat->quotations->status->name,
                    ] : null,
                    'currency' => $dat->quotations->currency ? [
                        'id' => $dat->quotations->currency->id,
                        'name' => $dat->quotations->currency->name,
                        'short_name' => $dat->quotations->currency->short_name,
                        'symbol' => $dat->quotations->currency->symbol,
                    ] : null,
                ] : [],
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
