<?php

namespace App\Project\Modules\System\Quotations\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicQuoteResource;
use App\Project\Modules\System\Quotations\QuotationShare;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Stateless JSON mirror of PublicItineraryController for the public
 * marketing site. The site calls GET to fetch a sanitized prefill
 * payload, lets the guest review and submit a booking via the existing
 * POST /api/save_booking endpoint, then calls /accept with the new
 * booking id to record the conversion.
 */
class PublicQuoteController extends Controller
{
    public function show(QuoteBuilderService $quoteBuilderService, string $token): JsonResponse
    {
        $version = $this->resolveVersion($token);

        if (!$version->viewed_at) {
            $version->update(['viewed_at' => now()]);
        }

        $context = $quoteBuilderService->buildDocumentData($version);

        $resource = (new PublicQuoteResource($version))->additional($context);

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $resource->toArray(request()),
        ]);
    }

    public function accept(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'fname' => 'nullable|string|max:120',
            'lname' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:160',
            'phone' => 'nullable|string|max:60',
            'booking_id' => 'nullable|integer|exists:bookings,id',
        ]);

        $version = $this->resolveVersion($token);

        $wonStatusId = QuotationStatus::query()
            ->whereRaw('LOWER(name) = ?', ['won'])
            ->value('id');

        $version->update([
            'accepted_at' => now(),
            'status' => 'accepted',
            'booking_id' => $validated['booking_id'] ?? $version->booking_id,
        ]);

        if ($wonStatusId && $version->quotation) {
            $version->quotation->update(['quotation_status_id' => $wonStatusId]);
        }

        QuotationShare::create([
            'quotation_version_id' => $version->id,
            'subject' => 'Public acceptance',
            'message' => $this->acceptanceAuditLine($validated),
            'channel' => 'public',
            'status' => 'accepted',
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Quote accepted.',
            'accepted_at' => $version->fresh()->accepted_at?->toIso8601String(),
        ]);
    }

    public function requestChanges(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'name' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:160',
        ]);

        $version = $this->resolveVersion($token);

        QuotationShare::create([
            'quotation_version_id' => $version->id,
            'subject' => $validated['name']
                ? sprintf('Public change request from %s', $validated['name'])
                : 'Public change request',
            'message' => $this->requestChangesAuditLine($validated),
            'channel' => 'public',
            'status' => 'requested_changes',
        ]);

        $version->update(['status' => 'changes_requested']);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Change request recorded.',
        ]);
    }

    protected function resolveVersion(string $token): QuotationVersion
    {
        return QuotationVersion::query()
            ->with(['quotation.inquiry.tourist.country', 'priceLines.currency', 'terms', 'paymentTerms', 'currency', 'trip'])
            ->where('public_token', $token)
            ->where('public_url_enabled', true)
            ->where(function ($query) {
                $query->whereNull('public_expires_at')
                    ->orWhere('public_expires_at', '>', now());
            })
            ->firstOrFail();
    }

    protected function acceptanceAuditLine(array $payload): string
    {
        $parts = array_filter([
            $payload['fname'] ?? null,
            $payload['lname'] ?? null,
        ]);
        $name = trim(implode(' ', $parts));
        $email = $payload['email'] ?? null;
        $phone = $payload['phone'] ?? null;
        $bookingId = $payload['booking_id'] ?? null;

        $line = $name ? "Accepted by {$name}" : 'Accepted via public link';
        if ($email) {
            $line .= " ({$email})";
        }
        if ($phone) {
            $line .= " — {$phone}";
        }
        if ($bookingId) {
            $line .= " — booking_id={$bookingId}";
        }

        return $line;
    }

    protected function requestChangesAuditLine(array $payload): string
    {
        $name = $payload['name'] ?? null;
        $email = $payload['email'] ?? null;
        $message = $payload['message'];

        $header = '';
        if ($name || $email) {
            $header = trim(sprintf('From %s%s', $name ?? '', $email ? " <{$email}>" : ''));
            $header .= "\n\n";
        }

        return $header . $message;
    }
}
