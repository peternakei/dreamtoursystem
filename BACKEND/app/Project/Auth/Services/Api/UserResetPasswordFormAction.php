<?php

namespace App\Project\Auth\Services\Api;

use App\Project\Modules\Core\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserResetPasswordFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if token is valid
        $valid = DB::table('password_reset_tokens')->where(['email' => $request->username, 'token' => $request->token])->first();
        if (!$valid) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Invalid token'
            ]);
        }

        //reset password
        $reset = User::where('username', $request->username)->update([
            'password' => bcrypt($request->password)
        ]);

        if (!$reset) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Failed to reset password'
            ]);
        }

        //delete token
        $delete = DB::table('password_reset_tokens')->where(['email' => $request->username, 'token' => $request->token])->delete();
        if (!$delete) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Failed to reset password'
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Password resetted successfully'
        ]);
    }
}
