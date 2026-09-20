<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeDestinationStatusFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $destination = Destination::where('uuid', $id)->first();
        $status = ($request->new_status == 1) ? true : false;

        //update
        $update = $destination->update(['is_active' => $status, 'updated_by' => Auth::user()->id]);
        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to change destination status'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Destination status changed succesfully'];
    }
}
