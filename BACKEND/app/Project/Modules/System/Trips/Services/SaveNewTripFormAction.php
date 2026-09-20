<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewTripFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //save
        $save = Trip::create([
            'name' => htmlspecialchars($request->name),
            'description' => $request->trip_description,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'last_booking_date' => $request->last_booking_date,
            'last_payment_date' => $request->last_payment_date,
            'trip_type_id' => $request->type,
            'trip_source_id' => $request->source,
            'trip_status_id' => 2,
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save trip details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip created successfully'];
    }
}
