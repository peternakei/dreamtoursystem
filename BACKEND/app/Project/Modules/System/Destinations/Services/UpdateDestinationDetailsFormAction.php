<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateDestinationDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $destination = Destination::where('uuid', $id)->first();

        //update
        $update = $destination->update([
            'name' => htmlspecialchars($request->name),
            'location_id' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'region_id' => $request->region,
            'description' => htmlspecialchars($request->description),
            'created_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update destination details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Destination updated successfully'];
    }
}
