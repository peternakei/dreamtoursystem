<?php

namespace App\Project\Modules\System\Quotations;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Quotations\Services\QuotePricingService;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index()
    {
        return $this->renderIndex();
    }

    public function won()
    {
        return $this->renderIndex('won');
    }

    public function open()
    {
        return $this->renderIndex('open');
    }

    public function lost()
    {
        return $this->renderIndex('lost');
    }

    public function create()
    {
        return redirect()->route('quotations.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('quotations.index');
    }

    public function show(string $id)
    {
        $quotation = Quotation::with([
            'tourist.country',
            'inquiry.tourist.country',
            'status',
            'createdFromTrip.banners',
            'currentVersion.currency',
            'currentVersion.days.destination.images',
            'currentVersion.priceLines.currency',
            'currentVersion.terms',
            'currentVersion.paymentTerms',
            'versions.currency',
        ])->where('uuid', $id)->firstOrFail();

        return view('web.system.quotation.show', [
            'quotation' => $quotation,
        ]);
    }

    public function edit(string $id)
    {
        return redirect()->route('quotations.show', $id);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('quotations.show', $id);
    }

    public function destroy(string $id)
    {
        return redirect()->route('quotations.index');
    }

    public function createFromInquiry(string $id, QuotePricingService $quotePricingService)
    {
        $inquiry = Inquiry::with([
            'tourist.country',
            'tripType',
            'serviceClass',
        ])->where('uuid', $id)->firstOrFail();

        $destinationIds = collect($inquiry->destinations ?? [])->filter()->values()->all();

        $trips = Trip::with(['banners', 'destinations.destination', 'tripType', 'addons'])
            ->when($inquiry->trip_type_id, fn($query) => $query->where('trip_type_id', $inquiry->trip_type_id))
            ->get()
            ->sortByDesc(function (Trip $trip) use ($destinationIds) {
                return $trip->destinations->whereIn('destination_id', $destinationIds)->count();
            })
            ->values();

        return view('web.system.quotation.create_from_inquiry', [
            'inquiry' => $inquiry,
            'trips' => $trips,
            'currencies' => Currency::all(),
            'serviceClasses' => ServiceClass::all(),
            'defaultCurrencyId' => $quotePricingService->defaultCurrencyId(),
        ]);
    }

    public function storeFromInquiry(Request $request, QuoteBuilderService $quoteBuilderService, string $id)
    {
        $validated = $request->validate([
            'trip_id' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'introduction' => 'nullable|string',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'service_class_id' => 'nullable|integer|exists:service_classes,id',
            'internal_notes' => 'nullable|string',
        ]);

        $inquiry = Inquiry::with(['tourist', 'serviceClass'])->where('uuid', $id)->firstOrFail();
        $version = $quoteBuilderService->createFromInquiry($inquiry, $validated, auth('web')->id());

        return redirect()
            ->route('quotation_versions.edit', $version->uuid)
            ->with('success', 'Quotation builder created successfully.');
    }

    public function duplicateVersion(QuoteBuilderService $quoteBuilderService, string $id)
    {
        $quotation = Quotation::with(['currentVersion.days.activities', 'currentVersion.priceLines', 'currentVersion.terms', 'currentVersion.paymentTerms', 'versions'])
            ->where('uuid', $id)
            ->firstOrFail();

        $version = $quoteBuilderService->duplicateVersion($quotation, auth('web')->id());

        return redirect()
            ->route('quotation_versions.edit', $version->uuid)
            ->with('success', 'A new quote version was created from the current draft.');
    }

    public function changeStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'new_status' => 'required|integer|exists:quotation_statuses,id',
        ]);

        $quotation = Quotation::where('uuid', $id)->firstOrFail();
        $quotation->update([
            'quotation_status_id' => $validated['new_status'],
            'updated_by' => auth('web')->id(),
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Quotation status changed successfully.',
            'redirect' => 'quotations/' . $id,
        ]);
    }

    protected function renderIndex(?string $status = null)
    {
        $query = Quotation::with([
            'tourist',
            'inquiry',
            'createdFromTrip',
            'status',
            'currentVersion.serviceClass',
        ])->orderByDesc('created_at');

        if ($status) {
            $query->whereHas('status', function ($builder) use ($status) {
                $builder->whereRaw('LOWER(name) = ?', [strtolower($status)]);
            });
        }

        $quotations = $query->get();

        $statusTitle = $status ? ucfirst($status) . ' Quotations' : 'All Quotations';

        return view('web.system.quotation.index', [
            'title' => 'Quotations',
            'sub_title' => $statusTitle,
            'quotations' => $quotations,
            'statuses' => QuotationStatus::all(),
        ]);
    }
}
