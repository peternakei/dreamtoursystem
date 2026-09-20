<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddTripPointFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        for ($i = 0; $i < count($request->title); $i++) {
            $save = TripPoint::create([
                'trip_id' => $trip->id,
                'title' => $request->title[$i],
                'description' => $request->description[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (!$save) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to create trip point'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip point created successfully'];
    }
}
