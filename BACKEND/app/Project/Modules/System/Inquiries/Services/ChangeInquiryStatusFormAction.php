<?php

namespace App\Project\Modules\System\Inquiries\Services;

use App\Project\Modules\System\Inquiries\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeInquiryStatusFormAction
{
    public function handle(Request $request, $id)
    {
        $inquiry = Inquiry::where('uuid', $id)->first();
        $status = ($request->new_status == 1) ? true : false;

        //update
        $update = $inquiry->update([
            'is_approved' => $status,
            'comments' => htmlspecialchars($request->comments)
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to change inquiry status'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Inquiry status changed successfully'];
    }
}
