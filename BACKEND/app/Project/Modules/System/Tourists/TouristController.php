<?php

namespace App\Project\Modules\System\Tourists;

use App\Project\Modules\System\Tourists\Services\ChangeTouristStatusFormAction;
use App\Project\Modules\System\Tourists\Services\SaveNewTouristFormAction;
use App\Project\Modules\System\Tourists\Services\UpdateTouristDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Tourists\Requests\ChangeTouristStatusFormRequest;
use App\Project\Modules\System\Tourists\Requests\CreateNewTouristFormRequest;
use App\Project\Modules\System\Tourists\Requests\EditTouristDetailsFormRequest;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Genders\Gender;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;

class TouristController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get tourists
        $tourists = Tourist::orderBy('created_at', 'desc')->get();
        $genders = Gender::all();
        $countries = Country::all();

        return view('web.system.tourist.index', ['title' => 'Tourists', 'sub_title' => 'All Tourists', 'tourists' => $tourists, 'genders' => $genders, 'countries' => $countries]);
    }

    public function active()
    {
        //get tourists
        $tourists = Tourist::where('is_active', true)->orderBy('created_at', 'desc')->get();
        $genders = Gender::all();
        $countries = Country::all();

        return view('web.system.tourist.index', ['title' => 'Tourists', 'sub_title' => 'Active Tourists', 'tourists' => $tourists, 'genders' => $genders, 'countries' => $countries]);
    }

    public function inactive()
    {
        //get tourists
        $tourists = Tourist::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $genders = Gender::all();
        $countries = Country::all();

        return view('web.system.tourist.index', ['title' => 'Tourists', 'sub_title' => 'Inactive Tourists', 'tourists' => $tourists, 'genders' => $genders, 'countries' => $countries]);
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
    public function store(CreateNewTouristFormRequest $request, SaveNewTouristFormAction $saveNewTouristFormAction)
    {
        //save
        $save = $saveNewTouristFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'tourists'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'tourists'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get tourist
        $tourist = Tourist::where('uuid', $id)->first();

        return view('web.system.tourist.show', ['tourist' => $tourist]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //get tourist
        $tourist = Tourist::where('uuid', $id)->first();
        $countries = Country::all();
        $genders = Gender::all();

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $tourist,
            'genders' => $genders,
            'countries' => $countries,
            'html' => '<input type="hidden" name="tourist_id" id="tourist_id" value="' . $tourist->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditTouristDetailsFormRequest $request, UpdateTouristDetailsFormAction $updateTouristDetailsFormAction, string $id)
    {
        //update
        $update = $updateTouristDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'tourists'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'tourists'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tourist = Tourist::where('uuid', $id)->first();

        User::where(['profile' => 'Tourist', 'profile_id' => $tourist->id])->delete();
        $delete = $tourist->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete tourist details',
                'redirect' => 'tourists'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Tourist deleted successfully',
            'redirect' => 'tourists'
        ]);
    }

    public function changeStatus(ChangeTouristStatusFormRequest $request, ChangeTouristStatusFormAction $changeTouristStatusFormAction, string $id)
    {
        //change
        $change = $changeTouristStatusFormAction->handle($request, $id);
        if (!$change) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to change tourist status',
                'redirect' => 'tourists/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Tourist status changed successfully',
            'redirect' => 'tourists/' . $id
        ]);
    }
}
