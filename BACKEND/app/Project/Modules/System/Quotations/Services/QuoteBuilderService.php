<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\System\Quotations\Services\QuotePricingService;
use App\Project\Modules\System\Quotations\Services\QuoteReferenceService;
use App\Project\Modules\System\Quotations\Services\QuoteAssetService;

use App\Project\Modules\System\Addons\Addon;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Quotations\QuotationPaymentTerm;
use App\Project\Modules\System\Quotations\QuotationPriceLine;
use App\Project\Modules\System\Quotations\QuotationTerm;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\Services\ItineraryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuoteBuilderService
{
    public function __construct(
        protected QuoteReferenceService $referenceService,
        protected QuotePricingService $pricingService,
        protected QuoteAssetService $assetService,
        protected ItineraryService $itineraryService,
    ) {
    }

    public function createFromInquiry(Inquiry $inquiry, array $attributes, int $userId): QuotationVersion
    {
        return DB::transaction(function () use ($inquiry, $attributes, $userId) {
            if (!$inquiry->request_reference) {
                $inquiry->update([
                    'request_reference' => $this->referenceService->nextRequestReference(),
                    'client_message' => $inquiry->client_message ?: $inquiry->description,
                    'received_at' => $inquiry->received_at ?: now(),
                    'updated_by' => $userId,
                ]);
            }

            $trip = !empty($attributes['trip_id']) ? Trip::query()->where('uuid', $attributes['trip_id'])->first() : null;
            $quotationNumber = $this->referenceService->nextQuotationNumber();

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'tourist_id' => $inquiry->tourist_id,
                'inquiry_id' => $inquiry->id,
                'created_from_trip_id' => $trip?->id,
                'quotation_date' => now()->toDateString(),
                'amount' => 0,
                'vat_amount' => 0,
                'total_amount' => 0,
                'currency_id' => ($attributes['currency_id'] ?? null) ?: $this->pricingService->defaultCurrencyId(),
                'exchange_rate' => 1,
                'quotation_status_id' => $this->pricingService->openStatusId(),
                'remarks' => $attributes['internal_notes'] ?? null,
                'created_by' => $userId,
            ]);

            $version = $this->createVersionRecord($quotation, $inquiry, $trip, [
                'title' => $attributes['title'] ?? ($trip?->name ?: $inquiry->tour_title ?: 'Safari Proposal'),
                'subtitle' => $attributes['subtitle'] ?? 'Crafted for ' . ($inquiry->tourist->name ?? 'your guest'),
                'introduction' => $attributes['introduction'] ?? $this->defaultIntroduction($inquiry, $trip),
                'company_profile' => $attributes['company_profile'] ?? $this->assetService->branding()['company_profile'],
                'currency_id' => ($attributes['currency_id'] ?? null) ?: $this->pricingService->defaultCurrencyId(),
                'service_class_id' => ($attributes['service_class_id'] ?? null) ?: $inquiry->service_class_id,
                'internal_notes' => $attributes['internal_notes'] ?? null,
            ], $userId, 1);

            $this->itineraryService->syncQuotationDays(
                $version,
                $this->itineraryService->seedQuotationDayPayloads($trip, $inquiry),
                $userId
            );

            $this->seedTermsFromTrip($version, $trip, $userId);
            $this->seedDefaultPaymentTerms($version, $userId);
            $this->pricingService->seedVersionPriceLines($version->fresh('trip'), $inquiry, $userId);

            return $version->fresh([
                'quotation',
                'trip',
                'currency',
                'days.activities',
                'days.destination.images',
                'priceLines.currency',
                'terms',
                'paymentTerms',
            ]);
        });
    }

    public function duplicateVersion(Quotation $quotation, int $userId): QuotationVersion
    {
        return DB::transaction(function () use ($quotation, $userId) {
            $source = $quotation->currentVersion ?: $quotation->versions()->with([
                'days.activities',
                'priceLines',
                'terms',
                'paymentTerms',
            ])->firstOrFail();

            $nextVersionNumber = ((int) $quotation->versions()->max('version_number')) + 1;

            $version = $this->createVersionRecord(
                $quotation,
                $quotation->inquiry,
                $source->trip,
                [
                    'title' => $source->title,
                    'subtitle' => $source->subtitle,
                    'introduction' => $source->introduction,
                    'company_profile' => $source->company_profile,
                    'currency_id' => $source->currency_id,
                    'service_class_id' => $source->service_class_id,
                    'internal_notes' => $source->internal_notes,
                ],
                $userId,
                $nextVersionNumber
            );

            $this->itineraryService->syncQuotationDays(
                $version,
                $source->days->map(function ($day) {
                    return [
                        'trip_day_id' => $day->trip_day_id,
                        'day_number' => $day->day_number,
                        'travel_date' => optional($day->travel_date)->toDateString(),
                        'title' => $day->title,
                        'destination_id' => $day->destination_id,
                        'accommodation_id' => $day->accommodation_id,
                        'accommodation_name' => $day->accommodation_name,
                        'accommodation_notes' => $day->accommodation_notes,
                        'stay_type' => $day->stay_type,
                        'nights' => $day->nights,
                        'breakfast' => $day->breakfast,
                        'lunch' => $day->lunch,
                        'dinner' => $day->dinner,
                        'description' => $day->description,
                        'activity_lines' => $day->activities->pluck('title')->filter()->implode("\n"),
                    ];
                })->all(),
                $userId
            );

            $this->syncTerms($version, [
                'included' => $source->terms->where('type', 'included')->values()->all(),
                'excluded' => $source->terms->where('type', 'excluded')->values()->all(),
            ], $userId);
            $this->syncPaymentTerms($version, $source->paymentTerms->all(), $userId);
            $this->pricingService->syncPriceLines($version, $source->priceLines->toArray(), $userId);

            return $version->fresh(['quotation', 'days.activities', 'priceLines', 'terms', 'paymentTerms']);
        });
    }

    public function syncVersionContent(QuotationVersion $version, array $payload, int $userId): QuotationVersion
    {
        return DB::transaction(function () use ($version, $payload, $userId) {
            $publicEnabled = (bool) ($payload['public_url_enabled'] ?? false);
            $coverImagePath = $this->resolveCoverImagePath($version, $payload);

            $versionUpdates = [
                'title' => ($payload['title'] ?? null) ?: $version->title,
                'subtitle' => ($payload['subtitle'] ?? null) ?: null,
                'introduction' => ($payload['introduction'] ?? null) ?: null,
                'highlights' => ($payload['highlights'] ?? null) ?: null,
                'agent_intro_letter' => ($payload['agent_intro_letter'] ?? null) ?: null,
                'company_profile' => ($payload['company_profile'] ?? null) ?: $this->assetService->branding()['company_profile'],
                'currency_id' => ($payload['currency_id'] ?? null) ?: $version->currency_id,
                'service_class_id' => ($payload['service_class_id'] ?? null) ?: null,
                'start_date' => ($payload['start_date'] ?? null) ?: null,
                'end_date' => ($payload['end_date'] ?? null) ?: null,
                'guest_count' => max((int) ($payload['guest_count'] ?? $version->guest_count), 1),
                'hide_price_breakdown' => (bool) ($payload['hide_price_breakdown'] ?? false),
                'hide_total_price' => (bool) ($payload['hide_total_price'] ?? false),
                'hide_terms' => (bool) ($payload['hide_terms'] ?? false),
                'hide_payment_terms' => (bool) ($payload['hide_payment_terms'] ?? false),
                'vat_enabled' => (bool) ($payload['vat_enabled'] ?? false),
                'cover_image_path' => $coverImagePath,
                'public_url_enabled' => $publicEnabled,
                'internal_notes' => ($payload['internal_notes'] ?? null) ?: null,
                'updated_by' => $userId,
            ];

            if ($publicEnabled && !$version->public_token) {
                $versionUpdates['public_token'] = Str::random(48);
            }

            $version->update($versionUpdates);

            $this->itineraryService->syncQuotationDays($version, $payload['days'] ?? [], $userId);
            $this->pricingService->syncPriceLines($version, $payload['price_lines'] ?? [], $userId);

            if (array_key_exists('included_addon_ids', $payload) || array_key_exists('excluded_addon_ids', $payload)) {
                $this->syncTermsFromAddons($version, [
                    'included' => $payload['included_addon_ids'] ?? [],
                    'excluded' => $payload['excluded_addon_ids'] ?? [],
                ], $userId);
            } else {
                $this->syncTerms($version, [
                    'included' => $payload['included_terms'] ?? [],
                    'excluded' => $payload['excluded_terms'] ?? [],
                ], $userId);
            }

            $this->syncPaymentTerms($version, $payload['payment_terms'] ?? [], $userId);

            return $version->fresh([
                'quotation',
                'quotation.inquiry.tourist.country',
                'trip.banners',
                'days.activities',
                'days.destination.images',
                'priceLines.currency',
                'terms',
                'paymentTerms',
            ]);
        });
    }

    public function buildDocumentData(QuotationVersion $version, bool $forPdf = false): array
    {
        $version->loadMissing([
            'quotation.inquiry.tourist.country',
            'quotation.status',
            'trip.banners',
            'trip.destinations.destination.images',
            'currency',
            'serviceClass',
            'days.activities',
            'days.destination.images',
            'days.accommodation.stayType',
            'days.accommodation.media',
            'priceLines.currency',
            'terms',
            'paymentTerms',
        ]);

        $branding = $this->assetService->branding();
        $trip = $version->trip;
        $inquiry = $version->quotation->inquiry;
        // Hero precedence:
        //   1. Trip banner — when this quote is built from an existing trip template.
        //   2. Uploaded cover — when set on the version (typical for from-scratch quotes).
        //   3. First day's destination image — fallback.
        $tripBanner = $trip ? $this->assetService->tripHero($trip, $forPdf) : null;
        $uploadedCover = $version->cover_image_path
            ? ($forPdf
                ? Storage::disk('public')->path($version->cover_image_path)
                : Storage::disk('public')->url($version->cover_image_path))
            : null;
        $firstDayDestination = $version->days->sortBy('sort_order')->first()?->destination;
        $firstDestinationImage = $firstDayDestination
            ? $this->assetService->destinationImage($firstDayDestination, $forPdf)
            : null;

        $defaultHero = $this->assetService->defaultHeroImage($forPdf);
        $heroImage = $tripBanner ?: ($uploadedCover ?: ($firstDestinationImage ?: $defaultHero));
        $days = $version->days->map(function ($day) use ($trip, $forPdf, $defaultHero) {
            $accommodation = $day->accommodation;

            $resolvedAccommodationName = $day->accommodation_name ?: $accommodation?->name;
            $resolvedAccommodationNotes = $day->accommodation_notes ?: $accommodation?->description;
            $resolvedStayType = $day->stay_type ?: $accommodation?->stayType?->name;

            $image = $this->assetService->destinationImage($day->destination, $forPdf)
                ?: $this->assetService->attachmentSource($accommodation?->primaryCover(), $forPdf)
                ?: $this->assetService->tripHero($trip, $forPdf)
                ?: $defaultHero;

            $accommodationImage = $this->assetService->attachmentSource(
                $accommodation?->primaryCover(),
                $forPdf
            );

            return [
                'id' => $day->id,
                'day_number' => $day->day_number,
                'travel_date' => $day->travel_date,
                'title' => $day->title ?: ('Day ' . $day->day_number),
                'destination' => $day->destination,
                'accommodation' => $accommodation,
                'accommodation_name' => $resolvedAccommodationName,
                'accommodation_notes' => $resolvedAccommodationNotes,
                'accommodation_image' => $accommodationImage,
                'stay_type' => $resolvedStayType,
                'nights' => $day->nights,
                'breakfast' => $day->breakfast,
                'lunch' => $day->lunch,
                'dinner' => $day->dinner,
                'description' => $day->description ?: $day->destination?->description,
                'activities' => $day->activities,
                'image' => $image,
            ];
        })->values();

        $routeDestinations = $days
            ->pluck('destination')
            ->filter()
            ->unique('id')
            ->values();

        $routePoints = $routeDestinations
            ->filter(fn($destination) => $destination && $destination->latitude !== null && $destination->longitude !== null)
            ->values()
            ->map(function ($destination, $index) {
                return [
                    'order' => $index + 1,
                    'name' => $destination->name,
                    'latitude' => (float) $destination->latitude,
                    'longitude' => (float) $destination->longitude,
                ];
            })
            ->values();

        $summaryTable = $days->map(function ($day) {
            $meals = collect([
                $day['breakfast'] ? 'B' : null,
                $day['lunch'] ? 'L' : null,
                $day['dinner'] ? 'D' : null,
            ])->filter()->values()->all();

            return [
                'day_number' => $day['day_number'],
                'destination_name' => $day['destination']->name ?? $day['title'],
                'accommodation_name' => $day['accommodation_name'],
                'stay_type' => $day['stay_type'],
                'meal_plan' => empty($meals) ? '-' : implode('/', $meals),
            ];
        })->values();

        $startDestination = optional($days->first()['destination'] ?? null)->name
            ?? optional($days->first())['title']
            ?? null;
        $endDestination = optional($days->last()['destination'] ?? null)->name
            ?? optional($days->last())['title']
            ?? null;

        $highlightsList = collect(preg_split('/\r?\n/', (string) $version->highlights))
            ->map(fn($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        if (empty($highlightsList)) {
            $highlightsList = $routeDestinations->pluck('name')->filter()->values()->all();
        }

        $companyContent = $this->assetService->companyContent();
        $agent = $this->assetService->agentForVersion($version);

        return [
            'branding' => $branding,
            'version' => $version,
            'document_reference' => $this->formatDocumentReference($version),
            'quotation' => $version->quotation,
            'inquiry' => $inquiry,
            'tourist' => $inquiry?->tourist,
            'trip' => $trip,
            'hero_image' => $heroImage,
            'default_hero_image' => $defaultHero,
            'days' => $days,
            'summary_table' => $summaryTable,
            'start_destination' => $startDestination,
            'end_destination' => $endDestination,
            'highlights_list' => $highlightsList,
            'agent' => $agent,
            'agent_intro_letter' => $version->agent_intro_letter ?: $version->introduction,
            'route_destinations' => $routeDestinations,
            'route_points' => $routePoints,
            'price_lines' => $version->priceLines->where('is_visible', true)->values(),
            'all_price_lines' => $version->priceLines,
            'included_terms' => $version->terms->where('type', 'included')->where('is_visible', true)->values(),
            'excluded_terms' => $version->terms->where('type', 'excluded')->where('is_visible', true)->values(),
            'payment_terms' => $version->paymentTerms->where('is_visible', true)->values(),
            'company_profile' => $version->company_profile ?: $branding['company_profile'],
            'company_mission' => $companyContent['mission'],
            'company_vision' => $companyContent['vision'],
            'company_address' => $companyContent['address'],
            'company_country' => $companyContent['country'],
            'company_email' => $companyContent['email'] ?: $branding['email'],
            'company_phone' => $companyContent['phone'] ?: $branding['phone'],
            'vehicles' => $this->assetService->vehicles($forPdf),
            'colofon' => $this->assetService->colofon(),
            'public_itinerary_url' => $this->assetService->publicItineraryUrl(
                $version->public_token,
                $forPdf ? 'quote_pdf' : 'digital_itinerary',
                $this->buildShareSlug($version)
            ),
            'public_booking_url' => $this->assetService->publicBookingUrl($version->public_token, $forPdf ? 'quote_pdf' : 'digital_itinerary'),
            'public_pdf_url' => $this->assetService->publicPdfUrl($version->public_token),
            'website_url' => $branding['website_url'],
            'generated_at' => now(),
            'document_mode' => $forPdf ? 'pdf' : 'screen',
        ];
    }

    public function buildShareSlug(QuotationVersion $version): string
    {
        $travellerName = trim((string) ($version->quotation?->inquiry?->tourist?->name ?? 'Guest'));
        $stamp = ($version->created_at ?? now())->format('Y-m-d-Hi');

        $slug = Str::slug('SBS Quotation for ' . $travellerName . ' ' . $stamp, '-');

        return $slug ?: 'SBS-Quotation-' . $stamp;
    }

    protected function resolveCoverImagePath(QuotationVersion $version, array $payload): ?string
    {
        $upload = $payload['cover_image'] ?? null;
        $remove = (bool) ($payload['remove_cover_image'] ?? false);
        $existing = $version->cover_image_path;

        if ($upload instanceof UploadedFile) {
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            return $upload->store('quotation-covers', 'public');
        }

        if ($remove && $existing) {
            Storage::disk('public')->delete($existing);
            return null;
        }

        return $existing;
    }

    protected function formatDocumentReference(QuotationVersion $version): string
    {
        if (str_starts_with((string) $version->reference_number, 'SBS-')) {
            return (string) $version->reference_number;
        }

        $stamp = ($version->created_at ?? now())->format('Ymd-Hi');

        return sprintf('SBS-%s-V%d', $stamp, $version->version_number);
    }

    public function syncTerms(QuotationVersion $version, array $groupedTerms, int $userId): void
    {
        $version->terms()->delete();

        foreach (['included', 'excluded'] as $type) {
            foreach (collect($groupedTerms[$type] ?? [])->values() as $index => $row) {
                $description = is_array($row) ? ($row['description'] ?? null) : ($row->description ?? null);
                $title = is_array($row) ? ($row['title'] ?? null) : ($row->title ?? null);
                $isVisible = is_array($row) ? (bool) ($row['is_visible'] ?? true) : (bool) ($row->is_visible ?? true);

                if (!$description) {
                    continue;
                }

                $version->terms()->create([
                    'type' => $type,
                    'title' => $title,
                    'description' => $description,
                    'is_visible' => $isVisible,
                    'sort_order' => $index + 1,
                    'created_by' => $userId,
                ]);
            }
        }
    }

    public function syncTermsFromAddons(QuotationVersion $version, array $groupedAddonIds, int $userId): void
    {
        $version->terms()->delete();

        $allIds = collect($groupedAddonIds['included'] ?? [])
            ->merge($groupedAddonIds['excluded'] ?? [])
            ->filter()
            ->unique()
            ->values();

        if ($allIds->isEmpty()) {
            return;
        }

        $addons = Addon::whereIn('id', $allIds)->get()->keyBy('id');

        foreach (['included', 'excluded'] as $type) {
            foreach (collect($groupedAddonIds[$type] ?? [])->values() as $index => $addonId) {
                $addon = $addons->get((int) $addonId);
                if (!$addon) {
                    continue;
                }

                $version->terms()->create([
                    'type' => $type,
                    'title' => null,
                    'description' => $addon->name,
                    'is_visible' => true,
                    'sort_order' => $index + 1,
                    'created_by' => $userId,
                ]);
            }
        }
    }

    public function syncPaymentTerms(QuotationVersion $version, array $terms, int $userId): void
    {
        $version->paymentTerms()->delete();

        foreach (collect($terms)->values() as $index => $row) {
            $description = is_array($row) ? ($row['description'] ?? null) : ($row->description ?? null);
            $title = is_array($row) ? ($row['title'] ?? null) : ($row->title ?? null);
            $isVisible = is_array($row) ? (bool) ($row['is_visible'] ?? true) : (bool) ($row->is_visible ?? true);

            if (!$description) {
                continue;
            }

            $version->paymentTerms()->create([
                'title' => $title,
                'description' => $description,
                'is_visible' => $isVisible,
                'sort_order' => $index + 1,
                'created_by' => $userId,
            ]);
        }
    }

    protected function createVersionRecord(
        Quotation $quotation,
        Inquiry $inquiry,
        ?Trip $trip,
        array $attributes,
        int $userId,
        int $versionNumber
    ): QuotationVersion {
        $referenceNumber = $this->referenceService->versionReference($quotation->quotation_number, $versionNumber);
        $startDate = $attributes['start_date'] ?? $inquiry->from_date ?? $trip?->from_date;
        $endDate = $attributes['end_date'] ?? $inquiry->to_date ?? $trip?->to_date;

        $version = $quotation->versions()->create([
            'trip_id' => $trip?->id,
            'version_number' => $versionNumber,
            'reference_number' => $referenceNumber,
            'title' => $attributes['title'],
            'subtitle' => $attributes['subtitle'],
            'introduction' => $attributes['introduction'],
            'company_profile' => $attributes['company_profile'],
            'currency_id' => $attributes['currency_id'] ?: $quotation->currency_id,
            'season_id' => $this->pricingService->resolveSeasonId($startDate),
            'service_class_id' => $attributes['service_class_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => $trip?->duration_days ?: 0,
            'duration_nights' => $trip?->duration_nights ?: 0,
            'guest_count' => $inquiry->guests ?: 1,
            'status' => 'draft',
            'public_token' => null,
            'public_url_enabled' => false,
            'internal_notes' => $attributes['internal_notes'] ?? null,
            'created_by' => $userId,
        ]);

        $quotation->update([
            'current_version_id' => $version->id,
            'updated_by' => $userId,
        ]);

        return $version;
    }

    protected function defaultIntroduction(Inquiry $inquiry, ?Trip $trip): string
    {
        $guestName = $inquiry->tourist->name ?? 'your guest';

        if ($trip) {
            return sprintf(
                'We have shaped this proposal for %s around %s, using our existing trip library, destination imagery, and pricing matrix so the quote is ready to review straight away.',
                $guestName,
                $trip->name
            );
        }

        return sprintf(
            'We have prepared this tailor-made safari concept for %s using the available destination library and your requested travel window.',
            $guestName
        );
    }

    protected function seedTermsFromTrip(QuotationVersion $version, ?Trip $trip, int $userId): void
    {
        $version->terms()->delete();

        if (!$trip) {
            return;
        }

        $trip->loadMissing('addons.addon');

        foreach ($trip->addons as $index => $tripAddon) {
            if (!$tripAddon->addon) {
                continue;
            }

            $version->terms()->create([
                'type' => $tripAddon->addon->is_include ? 'included' : 'excluded',
                'title' => null,
                'description' => $tripAddon->addon->name,
                'is_visible' => true,
                'sort_order' => $index + 1,
                'created_by' => $userId,
            ]);
        }
    }

    protected function seedDefaultPaymentTerms(QuotationVersion $version, int $userId): void
    {
        $version->paymentTerms()->delete();

        $version->paymentTerms()->create([
            'title' => 'Payment schedule',
            'description' => 'A 40% deposit secures the reservation and the remaining 60% is due before travel begins unless otherwise agreed in writing.',
            'is_visible' => true,
            'sort_order' => 1,
            'created_by' => $userId,
        ]);
    }
}
