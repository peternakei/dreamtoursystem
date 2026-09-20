<?php

namespace App\Project\Modules\System\Banks;

use App\Project\Modules\System\Banks\Services\SaveNewBankFormAction;
use App\Project\Modules\System\Banks\Services\UpdateBankDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Banks\Requests\CreateNewBankFormRequest;
use App\Project\Modules\System\Banks\Requests\EditBankDetailsFormRequest;
use App\Project\Modules\System\Banks\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get banks
        $banks = Bank::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.bank.index', ['title' => 'Banks', 'sub_title' => 'All Banks', 'banks' => $banks]);
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
    public function store(CreateNewBankFormRequest $request, SaveNewBankFormAction $saveNewBankFormAction)
    {
        //save
        $save = $saveNewBankFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'banks'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'banks'
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
        //get bank
        $bank = Bank::where('uuid', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $bank,
            'html' => '<input type="hidden" name="bank_id" id="bank_id" value="' . $bank->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditBankDetailsFormRequest $request, UpdateBankDetailsFormAction $updateBankDetailsFormAction, string $id)
    {
        //update
        $update = $updateBankDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'banks'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Bank updated successfully.',
            'redirect' => 'banks'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Bank::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete bank details',
                'redirect' => 'banks'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Bank deleted successfully',
            'redirect' => 'banks'
        ]);
    }
}
