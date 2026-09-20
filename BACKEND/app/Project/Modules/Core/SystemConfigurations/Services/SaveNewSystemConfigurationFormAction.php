<?php

namespace App\Project\Modules\Core\SystemConfigurations\Services;

use App\Project\Modules\Core\SystemConfigurations\SystemConfiguration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewSystemConfigurationFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check is exists
        $exists = SystemConfiguration::where(['system_configuration_type_id' => $request->config_type, 'is_active' => true])->get();
        if (count($exists) > 0) {
            //suspend
            $suspend = SystemConfiguration::where(['system_configuration_type_id' => $request->config_type, 'is_active' => 1])
                ->update(['is_active' => false, 'updated_at' => Carbon::now(), 'updated_by' => Auth::user()->id]);

            if (!$suspend) {
                DB::rollback();
                return ['status' => false, 'message' => 'Failed to suspend active configuration.'];
            }
        }

        //save new
        $save = SystemConfiguration::create([
            'system_configuration_type_id' => $request->config_type,
            'value' => $request->config_value,
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollback();
            return ['status' => false, 'message' => 'Failed to save system configuration.'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'System configuration created successfully.'];
    }
}
