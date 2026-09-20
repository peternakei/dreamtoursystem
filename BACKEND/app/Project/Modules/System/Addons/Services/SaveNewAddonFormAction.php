<?php

namespace App\Project\Modules\System\Addons\Services;

use App\Project\Modules\System\Addons\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewAddonFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $addon = Addon::where('name', 'like', '%' . $request->name . '%')->get();
        if (count($addon) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Addon with the name ' . $request->name . ' already exists.'];
        }

        //save
        $save = Addon::create([
            'name' => htmlspecialchars($request->name),
            'is_include' => ($request->is_include == 1) ? true : false,
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save addon details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Addon created successfully'];
    }
}
