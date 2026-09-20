<?php

namespace App\Project\Modules\System\Seasons\Services;

use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\SeasonDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewSeasonFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check if exists
        $season = Season::where('id', $request->season)->first();
        if (count($season->dates()->where('is_active', true)->get()) > 0) {
            //suspend
            $suspend = $season->dates()->update(['is_active' => false]);
            if (!$suspend) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Failed to suspend active dates'];
            }
        }

        //save
        $save = SeasonDate::create([
            'season_id' => $season->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::user()->id
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save season details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Season created successfully'];
    }
}
