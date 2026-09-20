<?php

namespace App\Project\Auth;

use App\Project\Auth\Services\SaveUserResettedPasswordFormAction;
use App\Project\Auth\Services\UserForgotPasswordFormAction;
use App\Project\Auth\Services\UserLoginFormAction;
use App\Project\Modules\Core\Users\Services\ChangeUserPasswordFormAction;
use App\Http\Controllers\Controller;
use App\Project\Auth\Requests\UserLoginFormRequest;
use App\Project\Auth\Requests\ForgotPasswordFormRequest;
use App\Project\Auth\Requests\SaveUserResettedPasswordFormRequest;
use App\Project\Modules\Core\Users\Requests\ChangeUserPasswordFormRequest;
use App\Project\Modules\Core\Users\User;
use App\Models\Web\System\Shop\ShopUser;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Web\Core\User\SendPasswordLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller
{
    public function authenticate(UserLoginFormRequest $request, UserLoginFormAction $userLoginFormAction)
    {

        $response = $userLoginFormAction->handle($request);
        if (!$response['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $response['message'],
            ]);
        }

        //get user role
        $authenticatedUser = Auth::user()->roles->pluck('name');

        //update login status
        User::where('id', Auth::user()->id)->update(['status' => 'LOGGEN_IN']);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'User logged in successfully',
            'user_role' => $authenticatedUser[0],
            'user_shop_id' => 0,
            'user_shop' => '',
            'redirect' => Auth::user()->must_change_password ? 'force-password-change' : 'dashboard/user',
        ]);
    }

    public function submitResetPassword(ForgotPasswordFormRequest $request, UserForgotPasswordFormAction $userForgotPasswordFormAction)
    {

        $resetLink = $userForgotPasswordFormAction->handle($request);
        if (!$resetLink['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to send reset password link.',
                'redirect' => 'forgot_password'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Password reset link sent successfully.',
            'redirect' => 'login'
        ]);
    }

    public function showResetPasswordForm(string $token)
    {
        //validate token
        $exists = DB::table('password_reset_tokens')->where(['token' => $token])->get();
        if (count($exists) <= 0) {
            return view('web.core.authentication.login');
        }
        return view('web.core.authentication.reset_password', ['token' => $token, 'email' => $exists[0]->email]);
    }

    public  function submitResetPasswordForm(SaveUserResettedPasswordFormRequest $request, SaveUserResettedPasswordFormAction $saveUserResettedPasswordFormAction)
    {
        $saveReset = $saveUserResettedPasswordFormAction->handle($request);
        if (!$saveReset) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to send reset user password.',
                'redirect' => 'login'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Password resetted successfully.',
            'redirect' => 'login'
        ]);
    }

    public function showForcePasswordChangeForm()
    {
        return view('web.core.authentication.force_password_change');
    }

    public function submitForcePasswordChangeForm(ChangeUserPasswordFormRequest $request, ChangeUserPasswordFormAction $changeUserPasswordFormAction)
    {
        $change = $changeUserPasswordFormAction->handle($request, Auth::user()->uuid);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message'],
            ]);
        }

        User::where('id', Auth::user()->id)->update(['status' => 'LOGGED_OUT']);
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Password changed successfully. Please login with your new password.',
            'redirect' => 'login',
        ]);
    }

    public function logout(Request $request)
    {

        //update login status
        User::where('id', Auth::user()->id)->update(['status' => 'LOGGED_OUT']);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        //Redirect user to login page
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'User logged out successfully',
            'redirect' => 'login'
        ]);
    }
}
