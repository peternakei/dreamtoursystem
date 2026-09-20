<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangeUserStatusFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $user = SystemUser::where('uuid', $id)->first();

        $update = $user->update(['is_active' => ($request->new_status), 'updated_at' => Carbon::now(), 'updated_by' => Auth::user()->id]);
        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update user status'];
        }

        //update logins
        $logins = User::where(['profile' => 'SystemUser', 'profile_id' => $user->id])->update(['is_active' => ($request->new_status), 'updated_at' => Carbon::now(), 'updated_by' => Auth::user()->id]);
        if (!$logins) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update user status'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User updated successfully'];
    }
}
