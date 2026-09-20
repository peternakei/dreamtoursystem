<?php

namespace App\Project\Modules\System\Banks\Services;

use App\Project\Modules\System\Banks\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateBankDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $bank = Bank::where('uuid', $id)->first();

        //update
        $update = $bank->update([
            'name' => htmlspecialchars($request->name),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update bank details.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Bank details updated successfully.'];
    }
}
