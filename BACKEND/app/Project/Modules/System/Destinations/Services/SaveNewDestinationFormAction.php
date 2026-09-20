<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewDestinationFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check exists
        $exists = Destination::where('name', 'like', '%' . htmlspecialchars($request->name) . '%')->get();
        if (count($exists) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Destination with name ' . $request->name . ' already exists'];
        }

        //save
        $save = Destination::create([
            'name' => htmlspecialchars($request->name),
            'location_id' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'region_id' => $request->region,
            'description' => htmlspecialchars($request->description),
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save destination details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Destination created successfully'];
    }
}
