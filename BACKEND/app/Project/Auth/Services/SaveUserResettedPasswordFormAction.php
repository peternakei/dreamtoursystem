<?php

namespace App\Project\Auth\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Project\Modules\Core\Users\SystemUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Project\Modules\Core\Users\User;

class SaveUserResettedPasswordFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $user = SystemUser::where('email', $request->email)->get();
        if (count($user) <= 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'User not found'];
        }

        $passwordRequest = DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
        if (!$passwordRequest) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to reset password'];
        }

        //change password
        $login = User::where(['username' => $request->email])->update([
            'password' => bcrypt($request->password),
            'must_change_password' => false,
            'updated_at' => Carbon::now(),
        ]);

        if (!$login) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to send password'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User password resetted successfully'];
    }
}
