<?php

namespace App\Project\Modules\System\Inquiries\ApiControllers;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Inquiries\Requests\Api\CreateServiceInquiryRequest;
use App\Project\Modules\System\Inquiries\ServiceOperationsController;
use App\Project\Modules\System\Inquiries\Services\ServiceInquiryService;
use App\Project\Modules\System\Pages\ContentSupport;
use Illuminate\Http\Request;

class ServiceInquiryController extends Controller
{
    public function store(CreateServiceInquiryRequest $request, ServiceInquiryService $service)
    {
        if ($request->bearerToken() && ! $request->user('sanctum')) {
            abort(401);
        }
        $inquiry = $service->create($request->validated(), $request->user('sanctum'));

        return ContentSupport::success(['inquiry' => ServiceInquiryService::resource($inquiry)], 'Inquiry submitted successfully');
    }

    private function owned(Request $request)
    {
        abort_unless($request->user()->profile === 'Tourist' && $request->user()->is_active, 403);

        return Inquiry::with('serviceDetails')->whereHas('serviceDetails', fn ($q) => $q->where('customer_user_id', $request->user()->id));
    }

    public function index(Request $request)
    {
        $request->validate(['limit' => 'nullable|integer|min:1|max:100', 'offset' => 'nullable|integer|min:0']);

        return ContentSupport::listing($this->owned($request)->latest('id'), $request, 'inquiries', fn ($i) => ServiceInquiryService::resource($i));
    }

    public function show(Request $request, string $uuid)
    {
        $inquiry = $this->owned($request)->where('uuid', $uuid)->firstOrFail();

        return ContentSupport::success(['inquiry' => ServiceInquiryService::resource($inquiry), 'quotes' => ServiceOperationsController::quotes($inquiry)]);
    }

    public function requestCancellation(Request $request, string $uuid)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $inquiry = $this->owned($request)->where('uuid', $uuid)->firstOrFail();
        $detail = $inquiry->serviceDetails;
        $detail->update(['operations' => array_merge($detail->operations ?? [], ['cancellation_requested' => true, 'cancellation_reason' => $request->reason, 'cancellation_requested_at' => now()->toIso8601String()])]);

        return ContentSupport::success(['inquiry' => ServiceInquiryService::resource($inquiry->fresh('serviceDetails'))], 'Cancellation request recorded for staff review');
    }
}
