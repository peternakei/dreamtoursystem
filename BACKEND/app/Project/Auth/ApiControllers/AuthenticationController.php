<?php

namespace App\Project\Auth\ApiControllers;

use App\Project\Auth\Services\Api\UserChangePasswordFormAction;
use App\Project\Auth\Services\Api\UserForgotPasswordFormAction;
use App\Project\Auth\Services\Api\UserLoginFormAction;
use App\Project\Auth\Services\Api\UserRegistrationFormAction;
use App\Project\Auth\Services\Api\UserResetPasswordFormAction;
use App\Http\Controllers\Controller;
use App\Project\Auth\Requests\Api\UserChangePasswordFormRequest;
use App\Project\Auth\Requests\Api\UserForgotPasswordFormRequest;
use App\Project\Auth\Requests\Api\UserLoginFormRequest;
use App\Project\Auth\Requests\Api\UserRegistrationFormRequest;
use App\Project\Auth\Requests\Api\UserResetPasswordFormRequest;

class AuthenticationController extends Controller
{
    //user login
    public function login(UserLoginFormRequest $request, UserLoginFormAction $userLoginFormAction)
    {
        return $userLoginFormAction->handle($request);
    }

    public function forgotPassword(UserForgotPasswordFormRequest $request, UserForgotPasswordFormAction $userForgotPasswordFormAction)
    {
        return $userForgotPasswordFormAction->handle($request);
    }

    public function resetPassword(UserResetPasswordFormRequest $request, UserResetPasswordFormAction $userResetPasswordFormAction)
    {
        return $userResetPasswordFormAction->handle($request);
    }

    public function changePassword(UserChangePasswordFormRequest $request, UserChangePasswordFormAction $userChangePasswordFormAction)
    {
        return $userChangePasswordFormAction->handle($request);
    }

    public function register(UserRegistrationFormRequest $request, UserRegistrationFormAction $userRegistrationFormAction)
    {
        return $userRegistrationFormAction->handle($request);
    }
}
