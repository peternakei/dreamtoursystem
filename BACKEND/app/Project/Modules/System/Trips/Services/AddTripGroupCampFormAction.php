<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\TripGroupCamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddTripGroupCampFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //exists
        $exists = TripGroupCamp::where('trip_group_id', $request->group)->where('camp', 'like', '%' . htmlspecialchars($request->camp) . '%')->get();
        if (count($exists) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Group camp with name ' . $request->camp . ' already exists'];
        }

        //save
        $save = TripGroupCamp::create([
            'trip_group_id' => $request->group,
            'camp' => htmlspecialchars($request->camp),
            'descriptions' => htmlspecialchars($request->description),
            'day' => $request->day,
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save group camp'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Group camp created successfully'];
    }
}
