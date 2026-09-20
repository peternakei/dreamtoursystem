<?php

namespace App\Project\Modules\System\Testimonials\Services\Api;

use App\Project\Modules\System\Testimonials\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaveNewTestimonialFormAction
{
    public function handle(Request $request)
    {
        $user = $request->user();

        DB::beginTransaction();

        //save
        $save = Testimonial::create([
            'tourist_id' => $user->profile_id,
            'comments' => htmlspecialchars($request->comments)
        ]);

        if (!$save) {
            DB::rollBack();
            return response()->json([
                'status' => "error",
                'code' => 100,
                "message" => 'Failed to save testimonials'
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => "success",
            'code' => 200,
            "message" => 'Testimonials received successfully'
        ]);
    }
}
