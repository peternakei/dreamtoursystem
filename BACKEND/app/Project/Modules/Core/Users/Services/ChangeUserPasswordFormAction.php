<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ChangeUserPasswordFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $login = User::find(Auth::id());

        if (!$login) {
            DB::rollBack();
            return ['status' => false, 'message' => 'User login not found'];
        }

        if ($request->password === $login->username) {
            DB::rollBack();
            return ['status' => false, 'message' => 'New password cannot be the same as your email address'];
        }

        $changePassword = $login->update([
            'password' => bcrypt($request->password),
            'must_change_password' => false,
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id,
        ]);

        if (!$changePassword) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to change user password'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'User password changed successfully'];
    }
}
