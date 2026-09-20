<?php

namespace App\Project\Auth\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserLoginFormAction
{
    public function handle(Request $request)
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        $remember = false;

        //Check if remember me is set
        if ($request->remember) {

            $remember = true;

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return ['status' => true, 'message' => 'User logged in successfully'];
            } else {
                return ['status' => false, 'message' => 'Incorrect username/password'];
            }
        } else {

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return ['status' => true, 'message' => 'User logged in successfully'];
            } else {
                return ['status' => false, 'message' => 'Incorrect username/password'];
            }
        }
    }
}
