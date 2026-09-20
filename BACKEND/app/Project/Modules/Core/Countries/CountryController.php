<?php

namespace App\Project\Modules\Core\Countries;

use App\Project\Modules\Core\Countries\Services\SaveNewCountryFormAction;
use App\Project\Modules\Core\Countries\Services\UpdateCountryDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Countries\Requests\CreateNewCountryFormRequest;
use App\Project\Modules\Core\Countries\Requests\EditCountryDetailsFormRequest;
use App\Project\Modules\Core\Countries\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get countries
        $countries = Country::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.country.index', ['title' => 'Countries', 'sub_title' => 'All Countries', 'countries' => $countries]);
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
    public function store(CreateNewCountryFormRequest $request, SaveNewCountryFormAction $saveNewCountryFormAction)
    {
        //save
        $save = $saveNewCountryFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'countries'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'countries'
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
        //get country
        $country = Country::where('uuid', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $country,
            'html' => '<input type="hidden" name="country_id" id="country_id" value="' . $country->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditCountryDetailsFormRequest $request, UpdateCountryDetailsFormAction $updateCountryDetailsFormAction, string $id)
    {
        //update
        $update = $updateCountryDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'countries'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Country updated successfully.',
            'redirect' => 'countries'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Country::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete country details',
                'redirect' => 'countries'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Country deleted successfully',
            'redirect' => 'countries'
        ]);
    }
}
