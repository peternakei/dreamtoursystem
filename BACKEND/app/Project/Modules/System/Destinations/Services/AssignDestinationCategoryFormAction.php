<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignDestinationCategoryFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $destination = Destination::where('uuid', $id)->first();

        for ($i = 0; $i < count($request->category); $i++) {

            //save destination category
            $save = DestinationCategory::create([
                'destination_id' => $destination->id,
                'category_id' => $request->category[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (!$save) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to save destination category'];
            }
        }

        DB::commit();
        return ['status' => true, 'message' => 'Category assigned successfully.'];
    }
}
