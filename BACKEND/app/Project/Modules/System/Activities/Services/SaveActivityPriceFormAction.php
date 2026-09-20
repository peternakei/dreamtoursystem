<?php

namespace App\Project\Modules\System\Activities\Services;

use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Activities\ActivityPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveActivityPriceFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $activity = Activity::where('uuid', $id)->first();

        //check price exists
        $exists = $activity->prices()->where(['age_group_id' => $request->age, 'duration_type_id' => $request->type, 'duration' => $request->duration, 'is_active' => true])->get();
        if (count($exists) > 0) {
            //suspend
            $suspend = $activity->prices()->where(['age_group_id' => $request->age, 'duration_type_id' => $request->type, 'duration' => $request->duration, 'is_active' => true])->update(['is_active' => false]);
            if (!$suspend) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to save activity price'];
            }
        }

        //save
        $save = ActivityPrice::create([
            'activity_id' => $activity->id,
            'age_group_id' => $request->age,
            'duration_type_id' => $request->type,
            'duration' => $request->duration,
            'price' => $request->price,
            'currency_id' => $request->currency,
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save activity price'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Activity price saved successfully'];
    }
}
