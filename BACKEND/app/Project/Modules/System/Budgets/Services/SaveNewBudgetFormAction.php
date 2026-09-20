<?php

namespace App\Project\Modules\System\Budgets\Services;

use App\Project\Modules\System\Seasons\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewBudgetFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $budget = Budget::where(['trip_id' => $request->trip, 'season_id' => $request->season, 'service_class_id' => $request->class, 'quantity' => $request->quantity])->get();
        if (count($budget) > 0) {
            //suspend
            $suspend = Budget::where(['trip_id' => $request->trip, 'season_id' => $request->season, 'service_class_id' => $request->class, 'quantity' => $request->quantity])->update(['is_active' => false]);
            if (!$suspend) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to suspend current budget'];
            }
        }

        //save
        $save = Budget::create([
            'trip_id' => $request->trip,
            'season_id' => $request->season,
            'service_class_id' => $request->class,
            'currency_id' => $request->currency,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save budget details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Budget created successfully'];
    }
}
