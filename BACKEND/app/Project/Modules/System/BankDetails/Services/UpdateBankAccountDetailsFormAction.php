<?php

namespace App\Project\Modules\System\BankDetails\Services;

use App\Project\Modules\System\BankDetails\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateBankAccountDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $bankDetail = BankDetail::where('uuid', $id)->first();

        //update
        $update = $bankDetail->update([
            'bank_id' => filter_var($request->bank, FILTER_VALIDATE_INT),
            'currency_id' => filter_var($request->currency, FILTER_VALIDATE_INT),
            'account_name' => htmlspecialchars($request->account_name),
            'account_number' => htmlspecialchars($request->account_number),
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update bank details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Bank details updated successfully'];
    }
}
