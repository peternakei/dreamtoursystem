<?php

namespace App\Project\Modules\System\Tourists\Services;

use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateTouristDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $tourist = Tourist::where('uuid', $id)->first();

        //update
        $update = $tourist->update([
            'name' => htmlspecialchars($request->name),
            'gender_id' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'country_id' => $request->country,
            'address' => $request->address,
            'updated_by' => Auth::user()->id,
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update tourist details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Tourist details updated successfully'];
    }
}
