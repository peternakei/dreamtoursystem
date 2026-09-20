<?php

namespace App\Project\Auth\Services\Api;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserForgotPasswordFormAction
{
    public function handle(Request $request)
    {
        //check if tourist number exists
        $user = User::where('username', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Email not found'
            ]);
        }

        //check if already requested reset
        $requestReset = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if ($requestReset) {
            //delete
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        }

        //save reset token
        $token = DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Password reset link sent successfully',
            'data' => [
                'username' => $request->email,
            ]
        ]);
    }
}
