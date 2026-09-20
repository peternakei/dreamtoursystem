<?php

namespace App\Project\Modules\Core\Locations;

use App\Project\Modules\Core\Locations\Services\SaveNewLocationFormAction;
use App\Project\Modules\Core\Locations\Services\UpdateLocationDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Locations\Requests\CreateNewLocationFormRequest;
use App\Project\Modules\Core\Locations\Requests\EditLocationDetailsFormRequest;
use App\Project\Modules\Core\Locations\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get locations
        $locations = Location::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.location.index', ['title' => 'Locations', 'sub_title' => 'All Locations', 'locations' => $locations]);
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
    public function store(CreateNewLocationFormRequest $request,SaveNewLocationFormAction $saveNewLocationFormAction)
    {
        //save
        $save = $saveNewLocationFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'locations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'locations'
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
        //get location
        $location = Location::where('uuid', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $location,
            'html' => '<input type="hidden" name="location_id" id="location_id" value="' . $location->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditLocationDetailsFormRequest $request,UpdateLocationDetailsFormAction $updateLocationDetailsFormAction, string $id)
    {
        //update
        $update = $updateLocationDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'locations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Location updated successfully.',
            'redirect' => 'locations'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Location::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete location details',
                'redirect' => 'locations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Location deleted successfully',
            'redirect' => 'locations'
        ]);
    }
}
