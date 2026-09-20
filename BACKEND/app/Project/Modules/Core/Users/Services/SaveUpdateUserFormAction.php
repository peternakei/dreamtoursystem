<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveUpdateUserFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $user = SystemUser::where('uuid', $id)->first();

        $update = $user->update([
            'name' => htmlspecialchars($request->name),
            'phone' => $request->phone,
            'email' => $request->email,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now()
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update user'];
        }

        $logins = User::where('username', $user->email)->update([
            'username' => $request->email
        ]);

        if (!$logins) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update user'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User updated successfully'];
    }
}
