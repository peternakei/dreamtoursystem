<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResetUserPasswordFormAction
{
    public function handle($id)
    {
        DB::beginTransaction();

        $user = SystemUser::where('uuid', $id)->first();

        $logins = User::where(['profile' => 'SystemUser', 'profile_id' => $user->id])->update([
            'password' => bcrypt($user->email),
            'must_change_password' => true,
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id
        ]);

        if (!$logins) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to reset user password'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User password resetted successfully'];
    }
}
