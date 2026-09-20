<?php

namespace App\Project\Modules\System\Activities\Services;

use App\Project\Modules\System\Activities\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewActivityFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $activity = Activity::where('name', 'like', '%' . $request->name . '%')->get();
        if (count($activity) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Activity with the name ' . $request->name . ' already exists.'];
        }

        //save
        $save = Activity::create([
            'name' => htmlspecialchars($request->name),
            'description' => htmlspecialchars($request->description),
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save activity details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Activity created successfully'];
    }
}
