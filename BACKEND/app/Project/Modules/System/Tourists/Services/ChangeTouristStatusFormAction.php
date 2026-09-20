<?php

namespace App\Project\Modules\System\Tourists\Services;

use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeTouristStatusFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $tourist = Tourist::where('uuid', $id)->first();
        $status = ($request->new_status == 1) ? true : false;

        //update
        $update = $tourist->update(['is_active' => $status, 'updated_by' => Auth::user()->id]);
        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to change tourist status'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Tourist status changed succesfully'];
    }
}
