<?php

namespace App\Http\Resources;

use App\Project\Modules\System\Quotations\QuotationVersion;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Sanitized public-facing payload for a quotation version, served from
 * GET /api/public/quote/{token}.
 *
 * Crucially, the `booking_prefill` block contains every field name that
 * SaveBookingFormRequest validates — so the public marketing site can
 * post the same shape directly to POST /api/save_booking without a
 * second mapping layer. Field-name parity is asserted by
 * tests/Unit/PublicQuoteResourceContractTest.php.
 *
 * @mixin QuotationVersion
 */
class PublicQuoteResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var QuotationVersion $version */
        $version = $this->resource;
        $context = $this->additional ?? [];

        $tourist = optional($version->quotation)->inquiry?->tourist;
        $countryName = optional($tourist)->country?->name;

        $price_lines = $version->priceLines
            ->where('is_visible', true)
            ->values()
            ->map(fn($line) => [
                'description' => $line->description,
                'traveler_type' => $line->traveler_type,
                'quantity' => (int) $line->quantity,
                'unit_price' => (float) $line->unit_price,
                'total_price' => (float) $line->total_price,
                'is_optional' => (bool) $line->is_optional,
                'currency' => optional($line->currency)->short_name,
            ])
            ->all();

        $included = $version->terms
            ->where('type', 'included')
            ->where('is_visible', true)
            ->pluck('description')
            ->filter()
            ->values()
            ->all();

        $excluded = $version->terms
            ->where('type', 'excluded')
            ->where('is_visible', true)
            ->pluck('description')
            ->filter()
            ->values()
            ->all();

        $payment_terms = $version->paymentTerms
            ->where('is_visible', true)
            ->values()
            ->map(fn($term) => [
                'title' => $term->title,
                'description' => $term->description,
            ])
            ->all();

        return [
            'reference' => $version->reference_number,
            'document_reference' => $context['document_reference'] ?? $version->reference_number,
            'title' => $version->title,
            'subtitle' => $version->subtitle,
            'currency' => optional($version->currency)->short_name ?? 'USD',
            'guest_count' => (int) $version->guest_count,
            'duration_days' => (int) $version->duration_days,
            'duration_nights' => (int) $version->duration_nights,
            'start_date' => optional($version->start_date)->toDateString(),
            'end_date' => optional($version->end_date)->toDateString(),
            'amount' => (float) $version->amount,
            'vat_amount' => (float) $version->vat_amount,
            'total_amount' => (float) $version->total_amount,
            'hide_price_breakdown' => (bool) $version->hide_price_breakdown,
            'hide_total_price' => (bool) $version->hide_total_price,
            'status' => $version->status,
            'accepted_at' => optional($version->accepted_at)->toIso8601String(),
            'viewed_at' => optional($version->viewed_at)->toIso8601String(),

            'agent' => $context['agent'] ?? null,
            'highlights' => $context['highlights_list'] ?? [],
            'days' => $context['summary_table'] ?? [],

            'price_lines' => $price_lines,
            'included' => $included,
            'excluded' => $excluded,
            'payment_terms' => $payment_terms,

            // === Booking prefill ===
            // Field names mirror SaveBookingFormRequest exactly so the public
            // site can post this shape to POST /api/save_booking without
            // re-mapping. See PublicQuoteResourceContractTest.
            'booking_prefill' => [
                'trip_uuid' => optional($version->trip)->uuid,
                'class_id' => $version->service_class_id,
                'guest_count' => (int) $version->guest_count,
                'start_date' => optional($version->start_date)->toDateString(),
                'gender' => optional($tourist)->gender_id,
                'country' => optional($tourist)->country_id,
                'phone' => optional($tourist)->phone,
                'email' => optional($tourist)->email,
                'fname' => $this->splitName(optional($tourist)->name)[0],
                'lname' => $this->splitName(optional($tourist)->name)[1],
                'note' => $version->title
                    ? sprintf('Booking from accepted quote %s — %s', $version->reference_number, $version->title)
                    : sprintf('Booking from accepted quote %s', $version->reference_number),
                'address' => optional($tourist)->address,
                'season_id' => $version->season_id,
            ],

            'tourist' => $tourist ? [
                'name' => $tourist->name,
                'email' => $tourist->email,
                'phone' => $tourist->phone,
                'country' => $countryName,
            ] : null,

            'links' => [
                'itinerary' => $context['public_itinerary_url'] ?? null,
                'booking' => $context['public_booking_url'] ?? null,
                'pdf' => $context['public_pdf_url'] ?? null,
                'accept' => url('/api/public/quote/' . $version->public_token . '/accept'),
                'request_changes' => url('/api/public/quote/' . $version->public_token . '/request-changes'),
                'submit_booking' => url('/api/save_booking'),
            ],
        ];
    }

    /**
     * Split a full name into [first, last]. Returns ['', ''] when null.
     * Public to allow re-use from tests asserting the prefill mapping.
     */
    public function splitName(?string $fullName): array
    {
        $fullName = trim((string) $fullName);
        if ($fullName === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/', $fullName);
        $first = array_shift($parts) ?? '';
        $last = trim(implode(' ', $parts));

        return [$first, $last];
    }
}
