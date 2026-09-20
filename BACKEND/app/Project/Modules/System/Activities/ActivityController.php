<?php

namespace App\Project\Modules\System\Activities;

use App\Project\Modules\System\Activities\Services\SaveActivityPriceFormAction;
use App\Project\Modules\System\Activities\Services\SaveNewActivityFormAction;
use App\Project\Modules\System\Activities\Services\UpdateActivityDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Activities\Requests\CreateActivityPriceFormRequest;
use App\Project\Modules\System\Activities\Requests\EditActivityDetailsFormRequest;
use App\Project\Modules\System\Activities\Requests\SaveNewActivityFormRequest;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\Core\AgeGroups\AgeGroup;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\DurationTypes\DurationType;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get activities
        $activities = Activity::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.activity.index', ['title' => 'Activities', 'sub_title' => 'All Activities', 'activities' => $activities]);
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
    public function store(SaveNewActivityFormRequest $request, SaveNewActivityFormAction $saveNewActivityFormAction)
    {
        //save
        $save = $saveNewActivityFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'activities'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'activities'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get activity
        $activity = Activity::where('uuid', $id)->first();
        $ageGroups = AgeGroup::all();
        $durationTypes = DurationType::all();
        $currencies = Currency::all();

        return view('web.system.configuration.activity.show', ['activity' => $activity, 'ageGroups' => $ageGroups, 'durationTypes' => $durationTypes, 'currencies' => $currencies]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //get activity
        $activity = Activity::where('uuid', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $activity,
            'html' => '<input type="hidden" name="activity_id" id="activity_id" value="' . $activity->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditActivityDetailsFormRequest $request, UpdateActivityDetailsFormAction $updateActivityDetailsFormAction, string $id)
    {
        //update
        $update = $updateActivityDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'activities'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Activity updated successfully.',
            'redirect' => 'activities'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Activity::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete activity details',
                'redirect' => 'activities'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Activity deleted successfully',
            'redirect' => 'activities'
        ]);
    }

    public function createPrice(CreateActivityPriceFormRequest $request, SaveActivityPriceFormAction $saveActivityPriceFormAction, string $id)
    {
        //save
        $save = $saveActivityPriceFormAction->handle($request, $id);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'activities/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'activities/' . $id
        ]);
    }
}
