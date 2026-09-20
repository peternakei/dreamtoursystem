<?php

namespace App\Project\Auth\Services;

use App\Jobs\User\UserPasswordRessettedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Project\Modules\Core\Users\SystemUser;
use App\Notifications\SendPasswordResetLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class UserForgotPasswordFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $token = Str::random(60);

        //check user
        $userExists = SystemUser::where('email', $request->username)->get();
        if (count($userExists) <= 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'User not found'];
        }

        //save
        DB::table('password_reset_tokens')->insert([
            'email' => $request->username,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user = SystemUser::where('email', $request->username)->first();

        $link = env('APP_URL') . '/reset_password/' . $token;

        $mailData = [
            'name' => $user->name,
            'email' => $user->email,
            'link' => $link
        ];

        //send password reset link
        dispatch((new UserPasswordRessettedJob($mailData)));

        DB::commit();
        return ['status' => true, 'message' => 'User forgot password email sent successfully'];
    }
}
