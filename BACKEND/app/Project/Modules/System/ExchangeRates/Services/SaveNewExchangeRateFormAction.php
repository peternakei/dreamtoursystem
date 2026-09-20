<?php

namespace App\Project\Modules\System\ExchangeRates\Services;

use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewExchangeRateFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $exchangeRate = ExchangeRate::where(['is_active' => true, 'currency_id' => $request->currency])->get();
        if (count($exchangeRate) > 0) {
            //suspend
            $suspend = ExchangeRate::where(['is_active' => true, 'currency_id' => $request->currency])->update(['is_active' => false]);
            if (!$suspend) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to suspend current exchange rate'];
            }
        }

        //save
        $save = ExchangeRate::create([
            'rate' => $request->rate,
            'currency_id' => $request->currency,
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save exchange rate'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Exchange Rate saved successfully'];
    }
}
