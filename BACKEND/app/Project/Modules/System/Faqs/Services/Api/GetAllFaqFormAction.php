<?php

namespace App\Project\Modules\System\Faqs\Services\Api;

use App\Project\Modules\System\Faqs\Faq;

class GetAllFaqFormAction
{
    public function handle()
    {
        $faqs = Faq::with([
            'faqCategory' => function ($query) {
                $query->select('id', 'name');
            }
        ])->get()->map(function ($faq) {
            return [
                'id' => $faq->id,
                'uuid' => $faq->uuid,
                'category' => $faq->faqCategory ? [
                    'id' => $faq->faqCategory->id,
                    'name' => $faq->faqCategory->name
                ] : null,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'order' => $faq->order,
                'status' => $faq->is_active ? true : false,
            ];
        });

        return response()->json([
            'status' => 'success',
            'code' => 100,
            'message' => 'Faqs fetched successfully',
            'data' => [
                'faqs' => $faqs
            ]
        ]);
    }
}
