<?php

namespace App\Project\Modules\System\Faqs;

use App\Project\Modules\System\Faqs\Services\ChangeFaqStatusFormAction;
use App\Project\Modules\System\Faqs\Services\SaveNewFaqFormAction;
use App\Project\Modules\System\Faqs\Services\UpdateFaqDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Faqs\Requests\ChangeFaqStatusFormRequest;
use App\Project\Modules\System\Faqs\Requests\CreateNewFaqFormRequest;
use App\Project\Modules\System\Faqs\Requests\EditFaqDetailsFormRequest;
use App\Project\Modules\System\Faqs\Faq;
use App\Project\Modules\System\Faqs\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get faqs
        $faqs = Faq::orderBy('created_at', 'desc')->get();
        $categories = FaqCategory::all();

        return view('web.system.faq.index', ['title' => 'Faqs', 'sub_title' => 'All Faqs', 'faqs' => $faqs, 'categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewFaqFormRequest $request, SaveNewFaqFormAction $saveNewFaqFormAction)
    {
        //save
        $save = $saveNewFaqFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'faqs'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'faqs'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get faq
        $faq = Faq::where('uuid', $id)->first();

        return view('web.system.faq.show', ['faq' => $faq]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //get faq details
        $faq = Faq::where('uuid', $id)->first();
        $categories = FaqCategory::all();

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $faq,
            'categories' => $categories,
            'html' => '<input type="hidden" name="faq_id" id="faq_id" value="' . $faq->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditFaqDetailsFormRequest $request, UpdateFaqDetailsFormAction $updateFaqDetailsFormAction, string $id)
    {
        //update
        $update = $updateFaqDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'faqs'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'faqs'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus(ChangeFaqStatusFormRequest $request, ChangeFaqStatusFormAction $changeFaqStatusFormAction, string $id)
    {
        //change
        $change = $changeFaqStatusFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message'],
                'redirect' => 'faqs/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $change['message'],
            'redirect' => 'faqs/' . $id
        ]);
    }
}
