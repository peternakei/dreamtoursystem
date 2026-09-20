<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddTripPriceFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        //exists
        if ($trip->trip_type_id == 1) {
            $exists = TripPrice::where(['trip_id' => $trip->id, 'age_group_id' => $request->age])->get();
            if (count($exists) > 0) {
                //suspend
                $suspend = TripPrice::where(['trip_id' => $trip->id, 'age_group_id' => $request->age])->update(['is_active' => false]);
                if (!$suspend) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Failed to suspend price'];
                }
            }
        } elseif ($trip->trip_type_id == 2) {
            $exists = TripPrice::where(['trip_group_id' => $request->group, 'age_group_id' => $request->age])->get();
            if (count($exists) > 0) {
                //suspend
                $suspend = TripPrice::where(['trip_group_id' => $request->group, 'age_group_id' => $request->age])->update(['is_active' => false]);
                if (!$suspend) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Failed to suspend price'];
                }
            }
        }

        $data = [
            'trip_id' => $trip->id,
            'price' => $request->price,
            'currency_id' => $request->currency,
            'age_group_id' => $request->age,
            'created_by' => Auth::user()->id,
        ];

        if (isset($request->group)) {
            $data['trip_group_id'] = $request->group;
        }

        //save
        $save = TripPrice::create($data);
        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save trip price'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip price added successfully'];
    }
}
