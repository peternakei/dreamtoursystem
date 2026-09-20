<?php

namespace App\Project\Modules\System\ExchangeRates\Services\Api;

use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use Illuminate\Http\Request;

class GetExchangeRateFormAction
{
    public function handle(Request $request)
    {
        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Exchange rate fetched successfully',
            'data' => [
                'rate' => ExchangeRate::where(['currency_id' => Currency::where(['uuid' => $request->currency])->value('id'), 'is_active' => true])->value('rate') ?? 0.00
            ]
        ]);
    }
}
