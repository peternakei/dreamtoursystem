<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignTripDestinationFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        for ($i = 0; $i < count($request->destination); $i++) {
            $save = TripDestination::create([
                'trip_id' => $trip->id,
                'destination_id' => $request->destination[$i],
                'description' => $request->description[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (!$save) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to create trip destination'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip destination created successfully'];
    }
}
