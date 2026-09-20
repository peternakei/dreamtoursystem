<?php

namespace App\Project\Modules\System\ExchangeRates;

use App\Project\Modules\System\ExchangeRates\Services\SaveNewExchangeRateFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\ExchangeRates\Requests\CreateNewExchangeRateFormRequest;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get rates
        $exchangeRates = ExchangeRate::orderBy('created_at', 'desc')->get();
        $currencies = Currency::all();

        return view('web.system.configuration.exchange_rate.index', ['title' => 'Exchange Rates', 'sub_title' => 'All Exchange Rates', 'exchangeRates' => $exchangeRates, 'currencies' => $currencies]);
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
    public function store(CreateNewExchangeRateFormRequest $request, SaveNewExchangeRateFormAction $saveNewExchangeRateFormAction)
    {
        //save
        $save = $saveNewExchangeRateFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'exchange_rates'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Exchange Rate details created successfully',
            'redirect' => 'exchange_rates'
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
