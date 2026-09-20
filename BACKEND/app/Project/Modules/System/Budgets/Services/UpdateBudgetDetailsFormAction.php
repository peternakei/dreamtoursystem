<?php

namespace App\Project\Modules\System\Budgets\Services;

use App\Project\Modules\System\Seasons\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateBudgetDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $budget = Budget::where('uuid', $id)->first();

        //update
        $update = $budget->update([
            'trip_id' => $request->trip,
            'season_id' => $request->season,
            'service_class_id' => $request->class,
            'currency_id' => $request->currency,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'updated_by' => Auth::user()->id
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update budget details.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Budget details updated successfully.'];
    }
}
