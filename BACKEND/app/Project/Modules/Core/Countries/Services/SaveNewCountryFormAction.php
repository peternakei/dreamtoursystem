<?php

namespace App\Project\Modules\Core\Countries\Services;

use App\Project\Modules\Core\Countries\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewCountryFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $country = Country::where('name', 'like', '%' . $request->name . '%')->orWhere('code', 'like', '%' . $request->code . '%')->get();
        if (count($country) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Country with the name ' . $request->name . ' already exists.'];
        }

        //save
        $save = Country::create([
            'name' => htmlspecialchars($request->name),
            'code' => htmlspecialchars($request->code),
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save country details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Country created successfully'];
    }
}
