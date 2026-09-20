<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignTripAddonFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        for ($i = 0; $i < count($request->addon); $i++) {
            $save = TripAddon::create([
                'trip_id' => $trip->id,
                'addon_id' => $request->addon[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (!$save) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to create trip addon'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip addon created successfully'];
    }
}
