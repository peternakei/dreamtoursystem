<?php

namespace App\Project\Modules\Core\Countries\Services;

use App\Project\Modules\Core\Countries\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateCountryDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $country = Country::where('uuid', $id)->first();

        //update
        $update = $country->update([
            'name' => htmlspecialchars($request->name),
            'code' => htmlspecialchars($request->code),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update country details.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Country details updated successfully.'];
    }
}
