<?php

namespace App\Project\Modules\System\Addons\Services;

use App\Project\Modules\System\Addons\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateAddonDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $addon = Addon::where('uuid', $id)->first();

        //update
        $update = $addon->update([
            'name' => htmlspecialchars($request->name),
            'is_include' => ($request->is_include == 1) ? true : false,
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update addon details.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Addon details updated successfully.'];
    }
}
