<?php

namespace App\Project\Modules\System\ExchangeRates\ApiControllers;

use App\Project\Modules\System\ExchangeRates\Services\Api\GetExchangeRateFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\ExchangeRates\Requests\Api\GetExchangeRateFormRequest;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function getRate(GetExchangeRateFormRequest $request, GetExchangeRateFormAction $getExchangeRateFormAction)
    {
        return $getExchangeRateFormAction->handle($request);
    }
}
