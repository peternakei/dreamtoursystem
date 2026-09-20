<?php

namespace App\Project\Modules\System\Likes\Services\Api;

use App\Project\Modules\System\Likes\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaveNewLikeFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $user = $request->user();

        $type = $request->type;

        $data = [
            'tourist_id' => $user->profile_id
        ];

        switch ($type) {
            case 'trip':
                $data['trip_id'] = $request->id;
                break;
            case 'trip_group':
                $data['trip_group_id'] = $request->id;
                break;
            case 'destination':
                $data['destination_id'] = $request->id;
                break;
            default:
                $data['trip_id'] = $request->id;
                break;
        }

        //save
        $save = Like::create($data);
        if (!$save) {
            DB::rollBack();
            return response()->json([
                'status' => "error",
                'code' => 100,
                "message" => 'Failed to save like'
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => "success",
            'code' => 200,
            "message" => 'Like saved succesfully'
        ]);
    }
}
