<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripCategory;
use App\Project\Modules\System\Trips\TripCategoryActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignTripCategoryActivityFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();
        $tripCategory = TripCategory::where(['trip_id' => $trip->id, 'category_id' => $request->trip_category])->first();

        for ($i = 0; $i < count($request->activity); $i++) {
            $save = TripCategoryActivity::create([
                'trip_category_id' => $tripCategory->id,
                'activity_id' => $request->activity[$i],
                'description' => $request->description[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (!$save) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to create trip category activity'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip category activity created successfully'];
    }
}
