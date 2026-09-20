<?php

namespace App\Project\Modules\Core\Locations\Services;

use App\Project\Modules\Core\Locations\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewLocationFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $location = Location::where('name', 'like', '%' . $request->name . '%')->get();
        if (count($location) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Location with the name ' . $request->name . ' already exists.'];
        }

        //save
        $save = Location::create([
            'name' => htmlspecialchars($request->name),
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save location details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Location created successfully'];
    }
}
