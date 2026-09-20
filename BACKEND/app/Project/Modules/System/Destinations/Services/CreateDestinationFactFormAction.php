<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationFact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateDestinationFactFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $destination = Destination::where('uuid', $id)->first();

        //save
        $save = DestinationFact::create([
            'destination_id' => $destination->id,
            'fact' => htmlspecialchars($request->fact),
            'sub_fact' => isset($request->sub_fact) ? htmlspecialchars($request->sub_fact) : '',
            'description' => htmlspecialchars($request->description),
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save destination fact'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Destination fact saved successfully'];
    }
}
