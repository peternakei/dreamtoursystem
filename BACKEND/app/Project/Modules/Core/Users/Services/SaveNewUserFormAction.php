<?php

namespace App\Project\Modules\Core\Users\Services;

use App\Jobs\User\NewUserCreatedJob;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use App\Models\Web\System\Property\Property;
use App\Models\Web\System\Property\PropertyUser;
use App\Models\Web\System\User\UserAccount;
use App\Notifications\NewSystemUserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class SaveNewUserFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $save = SystemUser::create([
            'name' => htmlspecialchars($request->name),
            'phone' => $request->phone,
            'email' => $request->email,
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to create new user'];
        }

        //save user logins
        $logins = User::create([
            'username' => $save->email,
            'password' => bcrypt($save->email),
            'must_change_password' => true,
            'profile' => 'SystemUser',
            'profile_id' => $save->id,
            'created_by' => Auth::user()->id
        ]);

        if (!$logins) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save new user logins'];
        }

        //assign user role
        $role = Role::findById($request->role);
        if ($role->name === 'SuperAdmin' && ! Auth::user()->hasRole('SuperAdmin')) {
            DB::rollBack();
            return ['status' => false, 'message' => 'You are not allowed to assign the SuperAdmin role'];
        }

        $logins->assignRole($role->name);

        $mailData = [
            'name' => $save->name,
            'email' => $save->email,
            'date' => date('Y-m-d H:i:s'),
            'role' => $role->name,
            'password' => $save->email,
        ];

        dispatch((new NewUserCreatedJob($mailData)));

        DB::commit();
        return ['status' => true, 'message' => 'User creatd successfully'];
    }
}
