<?php

namespace App\Project\Modules\System\BankDetails\Services;

use App\Project\Modules\System\BankDetails\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewBankAccountDetailFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();
        //check if exists
        $bankDetails = BankDetail::where(['account_number' => $request->account_number, 'currency_id' => $request->currency])->get();
        if (count($bankDetails) > 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Bank details with account number ' . $request->account_number . ' already exists'];
        }

        //save
        $save = BankDetail::create([
            'bank_id' => filter_var($request->bank, FILTER_VALIDATE_INT),
            'currency_id' => filter_var($request->currency, FILTER_VALIDATE_INT),
            'account_name' => htmlspecialchars($request->account_name),
            'account_number' => htmlspecialchars($request->account_number),
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save bank details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Bank details saved successfully'];
    }
}
