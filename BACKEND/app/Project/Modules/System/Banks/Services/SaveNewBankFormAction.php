<?php

namespace App\Project\Modules\System\Banks\Services;

use App\Project\Modules\System\Banks\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewBankFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $bank = Bank::where('name', 'like', '%' . $request->name . '%')->get();
        if (count($bank) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Bank with the name ' . $request->name . ' already exists.'];
        }

        //save
        $save = Bank::create([
            'name' => htmlspecialchars($request->name),
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save bank details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Bank created successfully'];
    }
}
