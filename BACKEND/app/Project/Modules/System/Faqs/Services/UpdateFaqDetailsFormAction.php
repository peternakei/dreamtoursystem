<?php

namespace App\Project\Modules\System\Faqs\Services;

use App\Project\Modules\System\Faqs\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateFaqDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $faq = Faq::where('uuid', $id)->first();

        //update
        $update = $faq->update([
            'faq_category_id' => $request->category,
            'question' => htmlspecialchars($request->question),
            'answer' => htmlspecialchars($request->answer),
            'order' => $request->order,
            'updated_by' => Auth::user()->id,
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update faq details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Faqs details updated successfully'];
    }
}
