<?php

namespace App\Project\Modules\Core\SystemConfigurations;

use App\Project\Modules\Core\SystemConfigurations\Services\SaveNewSystemConfigurationFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\SystemConfigurations\Requests\CreateNewSystemConfigurationFormRequest;
use App\Project\Modules\Core\SystemConfigurations\SystemConfiguration;
use App\Project\Modules\Core\SystemConfigurations\SystemConfigurationType;
use Illuminate\Http\Request;

class SystemConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //configurations
        $configurations = SystemConfiguration::orderBy('created_at', 'DESC')->get();
        $configTypes = SystemConfigurationType::all();

        return view('web.core.system_configuration.index', [
            'title' => 'Configurations',
            'sub_title' => 'System Configurations',
            'configurations' => $configurations,
            'configTypes' => $configTypes,
        ]);
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
    public function store(CreateNewSystemConfigurationFormRequest $request, SaveNewSystemConfigurationFormAction $saveNewSystemConfigurationFormAction)
    {
        $save = $saveNewSystemConfigurationFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'system_configurations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'system_configurations'
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
