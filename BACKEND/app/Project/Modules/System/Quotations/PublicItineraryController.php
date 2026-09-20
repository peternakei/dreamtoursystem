<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Bookings\Services\Api\SaveBookingFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Quotations\QuotationShare;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Quotations\Services\QuoteBookingService;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Quotations\Services\QuotePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicItineraryController extends Controller
{
    public function show(QuoteBuilderService $quoteBuilderService, string $token)
    {
        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->where(function ($query) {
                $query->whereNull('public_expires_at')
                    ->orWhere('public_expires_at', '>', now());
            })
            ->firstOrFail();

        if (!$version->viewed_at) {
            $version->update([
                'viewed_at' => now(),
            ]);
        }

        $data = $quoteBuilderService->buildDocumentData($version);

        return view('web.system.quotation.public.show', $data);
    }

    public function accept(string $token)
    {
        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->firstOrFail();

        $wonStatusId = QuotationStatus::query()
            ->whereRaw('LOWER(name) = ?', ['won'])
            ->value('id');

        $version->update([
            'accepted_at' => now(),
            'status' => 'accepted',
        ]);

        if ($wonStatusId) {
            $version->quotation->update([
                'quotation_status_id' => $wonStatusId,
            ]);
        }

        return redirect()
            ->route('public.itinerary.show', $token)
            ->with('success', 'Thank you. This proposal has been marked as accepted.');
    }

    public function reject(Request $request, string $token)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:2000',
        ]);

        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->firstOrFail();

        $lostStatusId = QuotationStatus::query()
            ->whereRaw('LOWER(name) = ?', ['lost'])
            ->value('id');

        $version->update([
            'status' => 'rejected',
        ]);

        if ($lostStatusId) {
            $version->quotation->update([
                'quotation_status_id' => $lostStatusId,
            ]);
        }

        if (!empty($validated['reason'])) {
            QuotationShare::create([
                'quotation_version_id' => $version->id,
                'subject' => 'Public rejection reason',
                'message' => $validated['reason'],
                'channel' => 'public',
                'status' => 'rejected',
            ]);
        }

        return redirect()
            ->route('public.itinerary.show', $token)
            ->with('success', 'Thank you for letting us know. The team will follow up if needed.');
    }

    public function requestChanges(Request $request, string $token)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->firstOrFail();

        QuotationShare::create([
            'quotation_version_id' => $version->id,
            'subject' => 'Public change request',
            'message' => $validated['message'],
            'channel' => 'public',
            'status' => 'requested_changes',
        ]);

        $version->update([
            'status' => 'changes_requested',
        ]);

        return redirect()
            ->route('public.itinerary.show', $token)
            ->with('success', 'Your requested changes were recorded successfully.');
    }

    public function confirm(QuoteBuilderService $quoteBuilderService, string $token)
    {
        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->where(function ($query) {
                $query->whereNull('public_expires_at')
                    ->orWhere('public_expires_at', '>', now());
            })
            ->firstOrFail();

        $data = $quoteBuilderService->buildDocumentData($version);

        return view('web.system.quotation.public.confirm', $data);
    }

    public function thankYou(QuoteBuilderService $quoteBuilderService, string $token)
    {
        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->firstOrFail();

        $data = $quoteBuilderService->buildDocumentData($version);

        return view('web.system.quotation.public.thank_you', $data);
    }

    public function book(
        Request $request,
        SaveBookingFormAction $action,
        QuoteBookingService $bookingService,
        string $token
    ) {
        $version = QuotationVersion::with(['trip', 'quotation.inquiry.tourist'])
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->firstOrFail();

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
                ->withErrors(['booking' => $result['message'] ?? 'Booking could not be completed. The team will follow up.']);
        }

        return redirect()
            ->route('public.itinerary.book.thank_you', $token)
            ->with('booking_reference', $result['booking_reference'] ?? null);
    }

    public function download(QuotePdfService $quotePdfService, string $token)
    {
        $version = QuotationVersion::query()
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->firstOrFail();

        if (!$version->pdf_path) {
            $quotePdfService->generate($version);
            $version->refresh();
        }

        return Storage::disk('public')->download($version->pdf_path, $quotePdfService->downloadName($version) . '.pdf');
    }
}
