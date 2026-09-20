<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddTripGroupFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        //check group exists
        $exists = TripGroup::where(['trip_id' => $trip->id])->where('group', 'like', '%' . htmlspecialchars($request->group) . '%')->get();
        if (count($exists) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Group with name ' . $request->group . ' already exists'];
        }

        //save
        $save = TripGroup::create([
            'trip_id' => $trip->id,
            'group' => htmlspecialchars($request->group),
            'size' => $request->size,
            'color' => $request->color,
            'departure_date' => $request->departure_date,
            'days' => $request->day,
            'description' => htmlspecialchars($request->description),
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to create trip group'];
        }

        DB::commit();
        return ['status' => true,'message' => 'Trip group created successfully'];
    }
}
