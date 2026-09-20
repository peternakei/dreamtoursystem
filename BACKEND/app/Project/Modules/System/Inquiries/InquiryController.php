<?php

namespace App\Project\Modules\System\Inquiries;

use App\Project\Modules\System\Inquiries\Services\ChangeInquiryStatusFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Inquiries\Requests\ChangeInquiryStatusFormRequest;
use App\Project\Modules\System\Inquiries\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get inquiries with relationships
        $inquiries = Inquiry::query()->when(!ServiceAccess::allowed(auth()->user()), fn($q)=>$q->whereDoesntHave('serviceDetails'))->withExists('serviceDetails')->with(['tourist.country', 'tripType', 'serviceClass', 'assignedTo', 'serviceDetails:id,inquiry_id,service_type'])->withCount('quotations')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('web.system.inquiry.index', ['title' => 'Inquiries', 'sub_title' => 'All Inquiries', 'inquiries' => $inquiries]);
    }

    public function processed()
    {
        //get inquiries with relationships
        $inquiries = Inquiry::query()->when(!ServiceAccess::allowed(auth()->user()), fn($q)=>$q->whereDoesntHave('serviceDetails'))->withExists('serviceDetails')->with(['tourist.country', 'tripType', 'serviceClass', 'assignedTo', 'serviceDetails:id,inquiry_id,service_type'])->withCount('quotations')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('web.system.inquiry.index', ['title' => 'Inquiries', 'sub_title' => 'Processed Inquiries', 'inquiries' => $inquiries]);
    }

    public function pending()
    {
        //get inquiries with relationships
        $inquiries = Inquiry::query()->when(!ServiceAccess::allowed(auth()->user()), fn($q)=>$q->whereDoesntHave('serviceDetails'))->withExists('serviceDetails')->with(['tourist.country', 'tripType', 'serviceClass', 'assignedTo', 'serviceDetails:id,inquiry_id,service_type'])->withCount('quotations')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('web.system.inquiry.index', ['title' => 'Inquiries', 'sub_title' => 'Pending Inquiries', 'inquiries' => $inquiries]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get inquiry with relationships
        $inquiry = Inquiry::query()->when(!ServiceAccess::allowed(auth()->user()), fn($q)=>$q->whereDoesntHave('serviceDetails'))->withExists('serviceDetails')->with(['tourist.country', 'tripType', 'serviceClass', 'assignedTo', 'serviceDetails:id,inquiry_id,service_type'])->withCount('quotations')
            ->with(['quotations.currency', 'quotations.status', 'quotations.currentVersion.currency'])
            ->where('uuid', $id)
            ->first();

        if (!$inquiry) {
            abort(404, 'Inquiry not found');
        }

        return view('web.system.inquiry.show', ['inquiry' => $inquiry]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus(ChangeInquiryStatusFormRequest $request, ChangeInquiryStatusFormAction $changeInquiryStatusFormAction, string $id)
    {
        //change
        $change = $changeInquiryStatusFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message'],
                'redirect' => 'inquiries/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $change['message'],
            'redirect' => 'inquiries/' . $id
        ]);
    }
}
