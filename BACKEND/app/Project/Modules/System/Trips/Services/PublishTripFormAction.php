<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublishTripFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        $destinations = $trip->destinations;
        foreach ($destinations as $dest) {

            //check if images were uploaded
            if (count($dest->destination->images) <= 0) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Upload destination image(s) first'];
            }

            //check destination categories
            if (count($dest->destination->categories) <= 0) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Add destination category(s) first'];
            }

            //check destination activities
            if (count($dest->destination->activities) <= 0) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Add destination activity(s) first'];
            }
        }

        //check trip budget
        if (count($trip->budgets()->get()) <= 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Configure trip budget first'];
        }

        $publish = $trip->update(['is_published' => true, 'trip_status_id' => 1, 'publish_remarks' => htmlspecialchars($request->remarks)]);
        if (!$publish) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to publish trip'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip published successfully'];
    }
}
