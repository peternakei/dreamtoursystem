<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\DestinationFact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateDestinationFactDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $update = DestinationFact::where('uuid', $id)->update([
            'fact' => htmlspecialchars($request->fact),
            'sub_fact' => isset($request->sub_fact) ? htmlspecialchars($request->sub_fact) : '',
            'description' => htmlspecialchars($request->description),
            'updated_by' => Auth::user()->id,
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update destination activity'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Destination activity updated successfully'];
    }
}
