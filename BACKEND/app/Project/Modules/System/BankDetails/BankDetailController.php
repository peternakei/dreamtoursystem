<?php

namespace App\Project\Modules\System\BankDetails;

use App\Project\Modules\System\BankDetails\Services\SaveNewBankAccountDetailFormAction;
use App\Project\Modules\System\BankDetails\Services\UpdateBankAccountDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\BankDetails\Requests\CreateNewBankDetailFormRequest;
use App\Project\Modules\System\BankDetails\Requests\EditBankAccountDetailFormRequest;
use App\Project\Modules\System\Banks\Bank;
use App\Project\Modules\System\BankDetails\BankDetail;
use App\Project\Modules\Core\Currencies\Currency;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get bank details
        $bankDetails = BankDetail::orderBy('created_at', 'desc')->get();
        $banks = Bank::all();
        $currencies = Currency::all();

        return view('web.system.configuration.bank_details.index', ['title' => 'Bank Details', 'sub_title' => 'All Bank Details', 'bankDetails' => $bankDetails, 'banks' => $banks, 'currencies' => $currencies]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewBankDetailFormRequest $request, SaveNewBankAccountDetailFormAction $saveNewBankAccountDetailFormAction)
    {
        //save
        $save = $saveNewBankAccountDetailFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'bank_details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Bank details created successfully',
            'redirect' => 'bank_details'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //get bank details
        $bankDetail = BankDetail::where('uuid', $id)->first();
        $banks = Bank::all();
        $currencies = Currency::all();

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $bankDetail,
            'banks' => $banks,
            'currencies' => $currencies,
            'bank' => $bankDetail->bank,
            'html' => '<input type="hidden" name="bank_detail_id" id="bank_detail_id" value="' . $bankDetail->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditBankAccountDetailFormRequest $request, UpdateBankAccountDetailsFormAction $updateBankAccountDetailsFormAction, string $id)
    {
        //update
        $update = $updateBankAccountDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'bank_details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Account details updated successfully',
            'redirect' => 'bank_details'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = BankDetail::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete account details',
                'redirect' => 'bank_details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Account details deleted successfully',
            'redirect' => 'bank_details'
        ]);
    }
}
