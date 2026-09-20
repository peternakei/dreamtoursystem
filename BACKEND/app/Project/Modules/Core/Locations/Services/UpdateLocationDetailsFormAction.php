<?php

namespace App\Project\Modules\Core\Locations\Services;

use App\Project\Modules\Core\Locations\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateLocationDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $location = Location::where('uuid', $id)->first();

        //update
        $update = $location->update([
            'name' => htmlspecialchars($request->name),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update location details.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Location details updated successfully.'];
    }
}
