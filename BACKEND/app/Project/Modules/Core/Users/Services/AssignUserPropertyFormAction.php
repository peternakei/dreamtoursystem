<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use App\Models\Web\System\Property\Property;
use App\Models\Web\System\Property\PropertyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignUserPropertyFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $user = SystemUser::where('uuid', $id)->first();

        $logins = User::where(['profile' => 'SystemUser', 'profile_id' => $user->id])->first();
        $properties = $request->property;

        foreach ($properties as $prop) {
            $property = Property::where(['id' => filter_var($prop, FILTER_VALIDATE_INT)])->first();
            $exists = PropertyUser::where(['property_id' => $property->id, 'user_id' => $logins->id])->get();
            if (count($exists) <= 0) {
                if (!isset($property->is_default)) {
                    $assign = PropertyUser::create([
                        'property_id' => $property->id,
                        'user_id' => $logins->id,
                        'created_by' => Auth::user()->id,
                    ]);

                    if (!$assign) {
                        DB::rollBack();
                        return ['status' => false, 'message' => 'Failed to assign user property'];
                    }
                }
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'User property assigned successfully'];
    }
}
