<?php

namespace App\Project\Modules\System\Faqs\Services;

use App\Project\Modules\System\Faqs\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeFaqStatusFormAction
{
    public function handle(Request $request, $id)
    {
        $faq = Faq::where('uuid', $id)->first();
        $status = ($request->new_status == 1) ? true : false;

        //update
        $update = $faq->update([
            'is_active' => $status
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to change faq status'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Faq status changed successfully'];
    }
}
