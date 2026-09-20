<?php

namespace App\Project\Auth\Services\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserChangePasswordFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => 'Current password does not match'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $change = $user->save();
        if (!$change) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to change password'
            ]);
        }

        //delete token
        $user->tokens()->delete();

        DB::commit();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Password changed successfully'
        ]);
    }
}
