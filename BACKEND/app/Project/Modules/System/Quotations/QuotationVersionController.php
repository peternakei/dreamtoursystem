<?php

namespace App\Project\Modules\System\Quotations;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Accommodations\StayType;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Addons\Addon;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Bookings\Services\Api\SaveBookingFormAction;
use App\Project\Modules\System\Quotations\Services\QuoteBookingService;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Quotations\Services\QuoteShareDraftService;
use Illuminate\Http\Request;

class QuotationVersionController extends Controller
{
    public function edit(string $id)
    {
        $version = QuotationVersion::with([
            'quotation.inquiry.tourist.country',
            'trip',
            'currency',
            'serviceClass',
            'days.activities.activity',
            'days.destination.images',
            'days.accommodation.stayType',
            'priceLines.currency',
            'terms',
            'paymentTerms',
        ])->where('uuid', $id)->firstOrFail();

        $accommodations = Accommodation::with(['stayType', 'destinations'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $currencies = Currency::all();

        $activeRates = ExchangeRate::where('is_active', true)
            ->get()
            ->keyBy('currency_id');

        // Rate semantics: how many of this currency equals 1 USD (USD is the base).
        // USD defaults to 1.0 when no row exists; other currencies fall back to 0 (= unknown).
        $exchangeRates = $currencies->mapWithKeys(function (Currency $currency) use ($activeRates) {
            $rate = (float) ($activeRates->get($currency->id)?->rate ?? 0);
            return [
                $currency->id => [
                    'short_name' => $currency->short_name,
                    'symbol' => $currency->symbol,
                    'rate' => $rate > 0 ? $rate : ($currency->short_name === 'USD' ? 1.0 : 0.0),
                ],
            ];
        })->all();

        $activities = Activity::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $addons = Addon::orderBy('name')->get();
        $includedAddons = $addons->where('is_include', true)->values();
        $excludedAddons = $addons->where('is_include', false)->values();

        $includedTermNames = $version->terms->where('type', 'included')->pluck('description')->filter()->all();
        $excludedTermNames = $version->terms->where('type', 'excluded')->pluck('description')->filter()->all();
        $selectedIncludedAddonIds = $includedAddons->whereIn('name', $includedTermNames)->pluck('id')->all();
        $selectedExcludedAddonIds = $excludedAddons->whereIn('name', $excludedTermNames)->pluck('id')->all();

        return view('web.system.quotation.builder', [
            'version' => $version,
            'destinations' => Destination::where('is_active', true)->orderBy('name')->get(),
            'currencies' => $currencies,
            'exchangeRates' => $exchangeRates,
            'activities' => $activities,
            'serviceClasses' => ServiceClass::all(),
            'accommodations' => $accommodations,
            'stayTypes' => StayType::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'accommodationLookup' => $accommodations->map(fn (Accommodation $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'stay_type_name' => $a->stayType->name ?? '',
                'primary_destination_id' => $a->primary_destination_id,
                'destination_ids' => $a->destinations->pluck('id')->all(),
            ])->values(),
            'includedAddons' => $includedAddons,
            'excludedAddons' => $excludedAddons,
            'selectedIncludedAddonIds' => $selectedIncludedAddonIds,
            'selectedExcludedAddonIds' => $selectedExcludedAddonIds,
        ]);
    }

    public function update(Request $request, QuoteBuilderService $quoteBuilderService, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'introduction' => 'nullable|string',
            'highlights' => 'nullable|string',
            'agent_intro_letter' => 'nullable|string',
            'company_profile' => 'nullable|string',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'service_class_id' => 'nullable|integer|exists:service_classes,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'guest_count' => 'required|integer|min:1',
            'internal_notes' => 'nullable|string',
            'days' => 'nullable|array',
            'price_lines' => 'nullable|array',
            'included_addon_ids' => 'nullable|array',
            'included_addon_ids.*' => 'integer|exists:addons,id',
            'excluded_addon_ids' => 'nullable|array',
            'excluded_addon_ids.*' => 'integer|exists:addons,id',
            'payment_terms' => 'nullable|array',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        $validated['hide_price_breakdown'] = $request->boolean('hide_price_breakdown');
        $validated['hide_total_price'] = $request->boolean('hide_total_price');
        $validated['hide_terms'] = $request->boolean('hide_terms');
        $validated['hide_payment_terms'] = $request->boolean('hide_payment_terms');
        $validated['public_url_enabled'] = $request->boolean('public_url_enabled');
        $validated['vat_enabled'] = $request->boolean('vat_enabled');
        $validated['cover_image'] = $request->file('cover_image');
        $validated['remove_cover_image'] = $request->boolean('remove_cover_image');

        $hasDestination = collect($validated['days'])->contains(fn ($day) => !empty($day['destination_id'] ?? null));
        if (!$hasDestination) {
            return back()
                ->withErrors(['days' => 'At least one day must have a destination selected before this quote can be saved.'])
                ->withInput();
        }
        $validated['days'] = $request->input('days', []);
        $validated['price_lines'] = $request->input('price_lines', []);
        $validated['included_addon_ids'] = $request->input('included_addon_ids', []);
        $validated['excluded_addon_ids'] = $request->input('excluded_addon_ids', []);
        $validated['payment_terms'] = $request->input('payment_terms', []);

        $version = QuotationVersion::with(['quotation', 'trip'])->where('uuid', $id)->firstOrFail();
        $quoteBuilderService->syncVersionContent($version, $validated, auth('web')->id());

        return redirect()
            ->route('quotation_versions.edit', $id)
            ->with('success', 'Quotation version updated successfully.');
    }

    public function preview(
        QuoteBuilderService $quoteBuilderService,
        QuoteShareDraftService $quoteShareDraftService,
        string $id
    ) {
        $version = QuotationVersion::where('uuid', $id)->firstOrFail();
        $data = $quoteBuilderService->buildDocumentData($version);
        $data['share'] = $quoteShareDraftService->buildDraftForVersion($version);

        return view('web.system.quotation.preview', $data);
    }

    /**
     * Internal booking endpoint — lets an authenticated agent confirm a booking
     * directly from the preview modal without needing the public link enabled.
     * Returns JSON for AJAX requests, otherwise redirects back to the preview.
     */
    public function book(
        Request $request,
        SaveBookingFormAction $action,
        QuoteBookingService $bookingService,
        string $id
    ) {
        $version = QuotationVersion::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'fname' => 'required|string|max:120',
            'lname' => 'nullable|string|max:120',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:40',
            'country' => 'nullable|string|max:120',
            'guest_count' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'note' => 'nullable|string|max:2000',
        ]);

        $result = $bookingService->book($version, $validated, $request, $action);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result, ($result['ok'] ?? false) ? 200 : 422);
        }

        if (!($result['ok'] ?? false)) {
            return back()
                ->withInput()
                ->withErrors(['booking' => $result['message'] ?? 'Booking could not be completed.']);
        }

        return redirect()
            ->route('quotation_versions.preview', $id)
            ->with('booking_reference', $result['booking_reference'] ?? null);
    }
}
