<?php

namespace App\Project\Auth\Services\Api;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserLoginFormAction
{
    public function handle(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $user = User::where('username', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => "error",
                'code' => 401,
                'message' => 'Invalid email or password'
            ], 401);
        }
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        $user->update(['status' => 'LOGGEN_IN']);

        $tourist = Tourist::where('id', $user->profile_id)->select('id', 'uuid', 'tourist_number', 'name', 'email', 'phone')->first();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Login successfully',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $tourist->id,
                    'name' => $tourist->name,
                    'email' => $tourist->email
                ]
            ]
        ]);
    }
}
