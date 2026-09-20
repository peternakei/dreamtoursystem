<?php

namespace App\Project\Modules\System\Subscriptions\Services\Api;

use App\Project\Modules\System\Subscriptions\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaveNewSubscriptionFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check exists
        $exists = Subscription::where(['email' => $request->email, 'is_active' => true])->get();
        if (count($exists) > 0) {
            DB::rollBack();
            return response()->json([
                'status' => "error",
                'code' => 100,
                "message" => 'Already subscribed',
                'data' => [
                    'email' => $request->email
                ]
            ]);
        }

        //save
        $save = Subscription::create([
            'email' => $request->email
        ]);

        if (!$save) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                "message" => 'Failed to subscribe',
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => "success",
            'code' => 200,
            "message" => 'Subscribed successfully',
            'data' => [
                'email' => $request->email
            ]
        ]);
    }
}
