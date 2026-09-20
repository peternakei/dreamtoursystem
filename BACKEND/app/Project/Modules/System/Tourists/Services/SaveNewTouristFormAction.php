<?php

namespace App\Project\Modules\System\Tourists\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewTouristFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //save
        $save = Tourist::create([
            'name' => htmlspecialchars($request->name),
            'gender_id' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'country_id' => $request->country,
            'address' => $request->address,
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save tourist details'];
        }

        //save logins
        $logins = User::create([
            'username' => $save->tourist_number,
            'password' => bcrypt($save->tourist_number),
            'profile' => 'Tourist',
            'profile_id' => $save->id,
            'created_by' =>  Auth::user()->id,
        ]);

        if (!$logins) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save logins details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Tourist details saved successfully'];
    }
}
