<?php

namespace App\Project\Modules\System\Activities\Services;

use App\Project\Modules\System\Activities\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateActivityDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $activity = Activity::where('uuid', $id)->first();

        //update
        $update = $activity->update([
            'name' => htmlspecialchars($request->name),
            'description' => htmlspecialchars($request->description),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update activity details.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Activity details updated successfully.'];
    }
}
