<?php

namespace App\Project\Modules\System\Faqs\Services;

use App\Project\Modules\System\Faqs\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveNewFaqFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //save
        $save = Faq::create([
            'faq_category_id' => $request->category,
            'question' => htmlspecialchars($request->question),
            'answer' => htmlspecialchars($request->answer),
            'order' => $request->order,
            'created_by' => Auth::user()->id,
        ]);

        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save faq details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Faqs details saved successfully'];
    }
}
