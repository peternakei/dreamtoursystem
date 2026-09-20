<?php

namespace App\Project\Modules\System\Addons;

use App\Project\Modules\System\Addons\Services\SaveNewAddonFormAction;
use App\Project\Modules\System\Addons\Services\UpdateAddonDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Addons\Requests\CreateNewAddonFormRequest;
use App\Project\Modules\System\Addons\Requests\EditAddonDetailsFormRequest;
use App\Project\Modules\System\Addons\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get addons
        $addons = Addon::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.addon.index', ['title' => 'Addons', 'sub_title' => 'All Addons', 'addons' => $addons]);
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
    public function store(CreateNewAddonFormRequest $request, SaveNewAddonFormAction $saveNewAddonFormAction)
    {
        //save
        $save = $saveNewAddonFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'addons'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'addons'
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
        //get addon
        $addon = Addon::where('uuid', $id)->first();
        $isIncludes = [
            [
                'id' => 1,
                'name' => 'Yes (Is Include)'
            ],
            [
                'id' => 2,
                'name' => 'No (Is Not Include)'
            ]
        ];
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $addon,
            'isIncludes' => $isIncludes,
            'html' => '<input type="hidden" name="addon_id" id="addon_id" value="' . $addon->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditAddonDetailsFormRequest $request, UpdateAddonDetailsFormAction $updateAddonDetailsFormAction, string $id)
    {
        //update
        $update = $updateAddonDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'addons'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Addon updated successfully.',
            'redirect' => 'addons'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Addon::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete addon details',
                'redirect' => 'addons'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Addon deleted successfully',
            'redirect' => 'addons'
        ]);
    }
}
