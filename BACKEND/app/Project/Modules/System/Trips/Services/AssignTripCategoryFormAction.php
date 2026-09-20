<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignTripCategoryFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        for ($i = 0; $i < count($request->category); $i++) {
            $save = TripCategory::create([
                'trip_id' => $trip->id,
                'category_id' => $request->category[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (!$save) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to create trip category'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip category created successfully'];
    }
}
