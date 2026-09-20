<?php

namespace App\Project\Modules\System\Testimonials\Services;

use App\Project\Modules\System\Testimonials\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeTestimonialStatusFormAction
{
    public function handle(Request $request, $id)
    {
        $testimonial = Testimonial::where('uuid', $id)->first();
        $status = ($request->new_status == 1) ? true : false;

        //update
        $update = $testimonial->update([
            'is_approved' => $status
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to change testimonial status'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Testimonial status changed successfully'];
    }
}
