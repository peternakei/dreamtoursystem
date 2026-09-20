@php
    $publicMode = $publicMode ?? false;
    // Booking is always available — trip-backed quotes route through SaveBookingFormAction,
    // trip-less quotes book directly using the version's stored amounts.
    $canBookFromQuote = true;
    // When true, the "Book This Safari" CTAs render as Bootstrap modal
    // triggers instead of anchor links — so the host page (preview or
    // public itinerary) can open the booking flow inline. PDF rendering
    // leaves this false because PDF viewers can't run JS modals.
    $inlineBookingModal = $inlineBookingModal ?? false;
    $palette = $branding['palette'] ?? [
        'forest' => '#AC5526',
        'sand' => '#E7D7B4',
        'ink' => '#AC5526',
        'sun' => '#0097DC',
        'mist' => '#F7F3EC',
    ];
    // Brand-tinted hero photo: the orange overlay is intentionally light
    // (~50%) so the underlying safari imagery reads clearly. A subtle dark
    // bottom gradient is layered on top to keep title/body text legible
    // against bright spots in the photo without dimming the whole surface.
    $resolvedHero = $hero_image ?: ($default_hero_image ?? null);
    $heroStyle = $resolvedHero
        ? "background-image: linear-gradient(180deg, rgba(0, 0, 0, 0) 55%, rgba(0, 0, 0, 0.35) 100%), linear-gradient(rgba(172, 85, 38, 0.5), rgba(172, 85, 38, 0.5)), url('{$resolvedHero}');"
        : '';
    // Same treatment, slightly stronger tint (~60%) for sections with denser
    // text content (pricing total, About panel) so the brand color reads
    // first but the photo is still clearly visible behind it.
    $brandedBackdropStyle = $resolvedHero
        ? "background-image: linear-gradient(rgba(172, 85, 38, 0.6), rgba(172, 85, 38, 0.6)), url('{$resolvedHero}'); background-size: cover; background-position: center;"
        : '';
    // Day card and gallery card image cells: when a day/destination has no
    // photo of its own, drop the same default hero photo in so the cell
    // doesn't render as a blank orange rectangle.
    $fallbackImage = $default_hero_image ?? null;
    $currencyCode = $version->currency->short_name ?? $quotation->currency->short_name ?? 'USD';
    $displayLogo = $document_mode === 'pdf' ? ($branding['logo_pdf_path'] ?? null) : ($branding['logo_url'] ?? null);
    $isPdf = $document_mode === 'pdf';
    $generatedLabel = $generated_at?->format('M d, Y H:i') ?? now()->format('M d, Y H:i');
    $travelWindow = optional($version->start_date)->format('M d, Y') ?? 'TBD';

    if ($version->end_date) {
        $travelWindow .= ' to ' . optional($version->end_date)->format('M d, Y');
    }

    $travelStart = optional($version->start_date)->format('l, F j, Y') ?? 'TBD';
    $travelEnd = optional($version->end_date)->format('l, F j, Y') ?? 'TBD';
@endphp

@if (($includeDocumentStyles ?? true) === true)
    <style>
        .quote-document {
            --quote-forest: {{ $palette['forest'] }};
            --quote-sand: {{ $palette['sand'] }};
            --quote-ink: {{ $palette['ink'] }};
            --quote-sun: {{ $palette['sun'] }};
            --quote-mist: {{ $palette['mist'] }};
            color: var(--quote-ink);
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            line-height: 1.55;
        }

        .quote-document * {
            box-sizing: border-box;
        }

        .quote-document a {
            color: inherit;
        }

        .quote-document .quote-sheet {
            max-width: 1120px;
            margin: 0 auto;
        }

        .quote-document .quote-section {
            background: #fff;
            border-radius: 22px;
            margin-bottom: 22px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(0, 52, 45, 0.08);
        }

        .quote-document .quote-brand-strip {
            background: rgba(172, 85, 38, 1);
            color: #fff;
            border-radius: 22px;
            margin-bottom: 18px;
            padding: 16px 24px;
        }

        .quote-document .quote-brand-logo {
            max-height: 44px;
            max-width: 145px;
            vertical-align: middle;
        }

        .quote-document .quote-cover {
            min-height: 500px;
            padding: 34px;
            background-position: center;
            background-size: cover;
            color: #fff;
            position: relative;
        }

        /* Solid brand color — matches the public site's primary surface.
           Chained selector wins over .quote-section { background: #fff }.
           Inline background-image (when a hero image exists) layers on top. */
        .quote-document .quote-section.quote-cover {
            background-color: rgba(172, 85, 38, 1);
            background-image: none;
        }

        .quote-document .quote-cover-media {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .quote-document .quote-cover-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .quote-document .quote-cover-overlay {
            position: absolute;
            inset: 0;
            background: rgba(172, 85, 38, 0.5);
        }

        .quote-document .quote-cover-content {
            position: relative;
            z-index: 2;
        }

        .quote-document .quote-cover-pdf-image {
            width: 38%;
            background: var(--quote-forest);
            vertical-align: top;
        }

        .quote-document .quote-cover-pdf-image img {
            width: 100%;
            height: 540px;
            object-fit: cover;
            display: block;
        }

        .quote-document .quote-cover-pdf-body {
            padding: 28px 30px 32px;
            background: rgba(172, 85, 38, 1);
            color: #fff;
            vertical-align: top;
        }

        .quote-document .quote-cover-logo {
            max-height: 56px;
            max-width: 170px;
            display: block;
        }

        .quote-document .quote-pill {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 12px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .quote-document .quote-cover-grid,
        .quote-document .quote-summary-table,
        .quote-document .quote-two-col,
        .quote-document .quote-price-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .quote-document .quote-cover-grid td {
            vertical-align: top;
        }

        .quote-document .quote-cover-title {
            font-size: 34px;
            line-height: 1.12;
            font-weight: 800;
            margin: 18px 0 10px;
        }

        .quote-document .quote-cover-subtitle {
            font-size: 16px;
            max-width: 580px;
            opacity: 0.95;
            margin-bottom: 16px;
        }

        .quote-document .quote-cover-card {
            width: 300px;
            background: rgba(255, 255, 255, 0.13);
            padding: 18px;
            border-radius: 18px;
        }

        .quote-document .quote-cover-card .k {
            display: block;
            font-size: 11px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            opacity: 0.8;
        }

        .quote-document .quote-cover-card .v {
            display: block;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .quote-document .quote-cta-bar {
            margin-top: 24px;
        }

        .quote-document .quote-cta-button {
            display: inline-block;
            margin: 0 12px 10px 0;
            padding: 12px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .quote-document .quote-cta-button.primary {
            background: var(--quote-sun);
            color: #fff;
        }

        .quote-document .quote-cta-button.secondary {
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.32);
        }

        .quote-document .quote-body {
            padding: 28px;
        }

        .quote-document .quote-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--quote-ink);
            margin: 0 0 8px;
        }

        .quote-document .quote-subtitle {
            color: #61736c;
            margin: 0;
        }

        .quote-document .quote-summary-box {
            background: var(--quote-mist);
            border-radius: 18px;
            padding: 18px;
        }

        .quote-document .quote-summary-box .label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            color: #7a847f;
            margin-bottom: 6px;
        }

        .quote-document .quote-summary-box .value {
            font-size: 19px;
            font-weight: 700;
        }

        .quote-document .quote-map-wrap {
            background: var(--quote-mist);
            border: 1px solid #e7ece9;
            border-radius: 18px;
            padding: 18px;
        }

        .quote-document .quote-leaflet-map {
            height: 260px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #dde6e1;
            background: #eef4f1;
        }

        .quote-document .quote-route-caption {
            font-size: 12px;
            color: #61736c;
            margin-top: 10px;
        }

        .leaflet-tooltip.quote-route-label {
            background: #ffffff;
            border: 1px solid #d6ddd9;
            color: #AC5526;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
            box-shadow: 0 4px 10px rgba(172, 85, 38, 0.12);
        }

        .leaflet-tooltip.quote-route-label::before {
            border-top-color: #d6ddd9;
        }

        .quote-document .quote-destination-chip {
            display: inline-block;
            margin: 0 8px 8px 0;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--quote-sand);
            color: var(--quote-ink);
            font-size: 12px;
            font-weight: 700;
        }

        .quote-document .quote-day-card {
            margin-bottom: 18px;
            border: 1px solid #e7ece9;
            border-radius: 18px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .quote-document .quote-day-table {
            width: 100%;
            border-collapse: collapse;
        }

        .quote-document .quote-day-table td {
            vertical-align: top;
        }

        .quote-document .quote-day-image {
            width: 36%;
            min-height: 240px;
            background-position: center;
            background-size: cover;
            display: table-cell;
            vertical-align: middle;
            background-color: var(--quote-forest);
            overflow: hidden;
        }

        .quote-document .quote-day-image img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }

        .quote-document .quote-day-content {
            width: 63%;
            display: table-cell;
            vertical-align: top;
            padding: 20px 22px;
        }

        .quote-document .quote-day-kicker {
            display: inline-block;
            background: var(--quote-forest);
            color: #fff;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .quote-document .quote-day-title {
            font-size: 22px;
            font-weight: 800;
            margin: 10px 0 8px;
        }

        .quote-document .quote-meal {
            display: inline-block;
            background: var(--quote-mist);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            margin-right: 6px;
            color: var(--quote-ink);
        }

        .quote-document .quote-price-table th,
        .quote-document .quote-price-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e7ece9;
            text-align: left;
        }

        .quote-document .quote-price-table thead th {
            background: var(--quote-mist);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .quote-document .quote-price-highlight {
            background: rgba(172, 85, 38, 1);
            background-size: cover;
            background-position: center;
            color: #fff;
            border-radius: 20px;
            padding: 22px;
        }

        .quote-document .quote-list {
            margin: 0;
            padding-left: 20px;
        }

        .quote-document .quote-list li {
            margin-bottom: 8px;
        }

        .quote-document .quote-gallery-card {
            border-radius: 18px;
            overflow: hidden;
            background: var(--quote-mist);
            border: 1px solid #e7ece9;
        }

        .quote-document .quote-gallery-image {
            height: 170px;
            background-size: cover;
            background-position: center;
            background-color: var(--quote-forest);
            overflow: hidden;
        }

        .quote-document .quote-gallery-image img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            display: block;
        }

        .quote-document .quote-company {
            background: rgba(172, 85, 38, 1);
            background-size: cover;
            background-position: center;
            color: #fff;
        }

        .quote-document .quote-company .quote-title,
        .quote-document .quote-company .quote-subtitle {
            color: #fff;
        }

        .quote-document .quote-footer-bar {
            background: #fff;
            border-radius: 22px;
            padding: 20px 24px;
            box-shadow: 0 10px 28px rgba(23, 52, 45, 0.08);
            color: var(--quote-ink);
        }

        .quote-document .quote-footer-bar a {
            color: var(--quote-forest);
            text-decoration: none;
            font-weight: 700;
        }

        .quote-document .quote-pdf-only {
            display: none;
        }

        @if ($isPdf)
            .quote-document .quote-screen-only {
                display: none !important;
            }

            .quote-document .quote-pdf-only {
                display: block;
            }

            .quote-document .quote-section {
                box-shadow: none;
                border: 1px solid #e7ece9;
            }

            .quote-document .quote-brand-strip,
            .quote-document .quote-footer-bar {
                box-shadow: none;
            }

            .quote-document .quote-cover {
                min-height: auto;
                padding: 0;
                background: none !important;
            }

            .quote-document .quote-cover-grid td {
                vertical-align: top;
            }

            .quote-document .quote-cover-card {
                width: auto;
                background: rgba(255, 255, 255, 0.12);
            }

            .quote-document .quote-day-image,
            .quote-document .quote-day-content {
                display: table-cell;
                float: none;
            }

            .quote-document .quote-day-image {
                width: 34%;
                min-height: 0;
            }

            .quote-document .quote-day-content {
                width: 66%;
                padding: 18px 20px;
            }

            .quote-document .quote-day-image img {
                height: 220px;
            }
        @endif
    </style>
@endif

<div class="quote-document">
    <div class="quote-sheet">
        <!-- <div class="quote-brand-strip">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 55%; vertical-align: middle;">
                        @if ($displayLogo)
                            <img src="{{ $displayLogo }}" alt="{{ $branding['company_name'] }}" class="quote-brand-logo">
                        @else
                            <strong>{{ $branding['company_name'] }}</strong>
                        @endif
                    </td>
                    <td style="width: 45%; text-align: right; vertical-align: middle;">
                        <div style="font-size: 13px; font-weight: 700;">{{ $branding['tagline'] }}</div>
                        <div style="font-size: 12px; opacity: 0.82;">
                            <a href="{{ $website_url }}" style="color: #fff; text-decoration: none;">{{ $website_url }}</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div> -->

        @if ($isPdf)
            <div class="quote-section quote-cover" style="padding: 0; min-height: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        @if ($hero_image)
                            <td class="quote-cover-pdf-image">
                                <img src="{{ $hero_image }}" alt="{{ $version->title }}">
                            </td>
                        @endif
                        <td class="quote-cover-pdf-body">
                            <table class="quote-cover-grid">
                                <tr>
                                    <td style="padding-right: 26px;">
                                @if ($displayLogo)
                                    <img src="{{ $displayLogo }}" alt="{{ $branding['company_name'] }}" class="quote-cover-logo">
                                @else
                                    <div class="quote-pill">{{ $branding['company_name'] }}</div>
                                @endif

                                <div class="quote-pill" style="margin-top: 22px;">Tailor-Made Proposal</div>
                                <div class="quote-cover-title">{{ $version->title }}</div>
                                @if ($version->subtitle)
                                    <div class="quote-cover-subtitle">{{ $version->subtitle }}</div>
                                @endif

                                @if ($agent_intro_letter)
                                    <div class="quote-cover-letter" style="margin-top: 18px; padding: 16px 18px; background:  rgba(172, 85, 38, 0.96); border-left: 3px solid #0097DC ; border-radius: 8px; font-size: 13px; line-height: 1.55;">
                                        <strong style="display:block; margin-bottom: 6px;">Dear {{ $tourist->name ?? 'Guest' }},</strong>
                                        <div style="white-space: pre-line;">{{ $agent_intro_letter }}</div>
                                        @if (!empty($agent['name']))
                                            <div style="margin-top: 12px; opacity: 0.92;">
                                                <strong>{{ $agent['name'] }}</strong>
                                                @if (!empty($agent['email']))
                                                    <span style="display:block; font-size:12px; opacity:0.85;">{{ $agent['email'] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="quote-cta-bar">
                                    @if ($canBookFromQuote)
                                        @if ($inlineBookingModal)
                                            <button type="button" class="quote-cta-button primary" data-bs-toggle="modal" data-bs-target="#quoteBookingModal" style="border: 0; cursor: pointer; font-family: inherit;">Book This Safari</button>
                                        @elseif ($publicMode)
                                            <a href="{{ route('public.itinerary.book.confirm', $version->public_token) }}" class="quote-cta-button primary">Book This Safari</a>
                                        @elseif ($public_booking_url)
                                            <a href="{{ $public_booking_url }}" class="quote-cta-button primary">Book This Safari</a>
                                        @endif
                                    @endif
                                    @if ($website_url)
                                        <a href="{{ $website_url }}" @if ($publicMode) target="_blank" rel="noopener" @endif class="quote-cta-button secondary">Visit Our Website</a>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 320px;">
                                <div class="quote-cover-card">
                                    <span class="k">Reference</span>
                                    <span class="v">{{ $document_reference }}</span>
                                    <span class="k">Prepared For</span>
                                    <span class="v">{{ $tourist->name ?? 'Guest' }}</span>
                                    <span class="k">Travel Window</span>
                                    <span class="v">{{ $travelWindow }}</span>
                                    <span class="k">Travelers</span>
                                    <span class="v">{{ $version->guest_count }} traveler{{ $version->guest_count == 1 ? '' : 's' }}</span>
                                    <span class="k">Prepared By</span>
                                    <span class="v">{{ $agent['name'] ?? $branding['company_name'] }}</span>
                                    @if (!empty($agent['email']))
                                        <span class="k">Agent Email</span>
                                        <span class="v" style="word-break: break-all;">{{ $agent['email'] }}</span>
                                    @endif
                                    <span class="k">Generated</span>
                                    <span class="v">{{ $generatedLabel }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <div class="quote-section quote-cover" style="{{ $heroStyle }}">
                <div class="quote-cover-content">
                    <table class="quote-cover-grid">
                        <tr>
                            <td style="padding-right: 26px;">
                                @if ($displayLogo)
                                    <img src="{{ $displayLogo }}" alt="{{ $branding['company_name'] }}" class="quote-cover-logo">
                                @else
                                    <div class="quote-pill">{{ $branding['company_name'] }}</div>
                                @endif

                                <div class="quote-pill" style="margin-top: 24px;">Tailor-Made Proposal</div>
                                <div class="quote-cover-title">{{ $version->title }}</div>
                                @if ($version->subtitle)
                                    <div class="quote-cover-subtitle">{{ $version->subtitle }}</div>
                                @endif

                                @if ($agent_intro_letter)
                                    <div class="quote-cover-letter" style="margin-top: 18px; padding: 16px 18px; background: rgba(255,255,255,0.13); border-left: 3px solid #0097DC; border-radius: 8px; font-size: 13px; line-height: 1.55;">
                                        <strong style="display:block; margin-bottom: 6px;">Dear {{ $tourist->name ?? 'Guest' }},</strong>
                                        <div style="white-space: pre-line;">{{ $agent_intro_letter }}</div>
                                        @if (!empty($agent['name']))
                                            <div style="margin-top: 12px; opacity: 0.92;">
                                                <strong>{{ $agent['name'] }}</strong>
                                                @if (!empty($agent['email']))
                                                    <span style="display:block; font-size:12px; opacity:0.85;">{{ $agent['email'] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @elseif ($version->introduction)
                                    <div class="quote-cover-subtitle">{{ $version->introduction }}</div>
                                @endif

                                <div class="quote-cta-bar">
                                    @if ($canBookFromQuote)
                                        @if ($inlineBookingModal)
                                            <button type="button" class="quote-cta-button primary" data-bs-toggle="modal" data-bs-target="#quoteBookingModal" style="border: 0; cursor: pointer; font-family: inherit;">Book This Safari</button>
                                        @elseif ($publicMode)
                                            <a href="{{ route('public.itinerary.book.confirm', $version->public_token) }}" class="quote-cta-button primary">Book This Safari</a>
                                        @elseif ($public_booking_url)
                                            <a href="{{ $public_booking_url }}" class="quote-cta-button primary">Book This Safari</a>
                                        @endif
                                    @endif
                                    @if ($website_url)
                                        <a href="{{ $website_url }}" @if ($publicMode) target="_blank" rel="noopener" @endif class="quote-cta-button secondary">Visit Our Website</a>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 320px;">
                                <div class="quote-cover-card">
                                    <span class="k">Reference</span>
                                    <span class="v">{{ $document_reference }}</span>
                                    <span class="k">Prepared For</span>
                                    <span class="v">{{ $tourist->name ?? 'Guest' }}</span>
                                    <span class="k">Travel Window</span>
                                    <span class="v">{{ $travelWindow }}</span>
                                    <span class="k">Travelers</span>
                                    <span class="v">{{ $version->guest_count }} traveler{{ $version->guest_count == 1 ? '' : 's' }}</span>
                                    <span class="k">Prepared By</span>
                                    <span class="v">{{ $agent['name'] ?? $branding['company_name'] }}</span>
                                    @if (!empty($agent['email']))
                                        <span class="k">Agent Email</span>
                                        <span class="v" style="word-break: break-all;">{{ $agent['email'] }}</span>
                                    @endif
                                    <span class="k">Generated</span>
                                    <span class="v">{{ $generatedLabel }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        @if ($isPdf)
            <div style="page-break-before: always;"></div>
        @endif

        {{-- Summary page: tabular day-by-day overview, start/end destinations, highlights --}}
        <div class="quote-section">
            <div class="quote-body">
                <div class="quote-title">Summary</div>
                <p class="quote-subtitle">{{ $version->title }}</p>

                <table style="width: 100%; border-collapse: collapse; margin: 18px 0 8px;">
                    <tr>
                        <td style="width: 50%; padding: 8px 12px; background: #f7f3ec; border-radius: 8px;">
                            <span style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #6f7d76;">Start Tour</span>
                            <div style="font-weight: 800;">{{ $travelStart ?? optional($version->start_date)->format('l, F j, Y') ?? 'TBD' }}</div>
                        </td>
                        <td style="width: 50%; padding: 8px 12px; background: #f7f3ec; border-radius: 8px;">
                            <span style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #6f7d76;">End Tour</span>
                            <div style="font-weight: 800;">{{ $travelEnd ?? optional($version->end_date)->format('l, F j, Y') ?? 'TBD' }}</div>
                        </td>
                    </tr>
                </table>

                @if ($start_destination)
                    <p style="margin: 14px 0 4px;"><strong>Start Destination:</strong> {{ $start_destination }}</p>
                @endif

                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px;">
                    <thead>
                        <tr style="background: #AC5526; color: #fff;">
                            <th style="text-align: left; padding: 10px 12px; width: 12%;">Days</th>
                            <th style="text-align: left; padding: 10px 12px; width: 33%;">Main Destination</th>
                            <th style="text-align: left; padding: 10px 12px; width: 35%;">Accommodation</th>
                            <th style="text-align: left; padding: 10px 12px; width: 20%;">Meal Plan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary_table as $row)
                            <tr style="border-bottom: 1px solid #e7ece9;">
                                <td style="padding: 10px 12px; font-weight: 700; color: #AC5526;">Day {{ $row['day_number'] }}</td>
                                <td style="padding: 10px 12px;">{{ $row['destination_name'] ?: '—' }}</td>
                                <td style="padding: 10px 12px;">
                                    {{ $row['accommodation_name'] ?: '—' }}
                                    @if ($row['stay_type'])
                                        <span style="display: block; font-size: 11px; color: #6f7d76;">{{ $row['stay_type'] }}</span>
                                    @endif
                                </td>
                                <td style="padding: 10px 12px;">{{ $row['meal_plan'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($end_destination)
                    <p style="margin: 12px 0 4px;"><strong>End Destination:</strong> {{ $end_destination }}</p>
                @endif

                @if (!empty($highlights_list))
                    <div style="margin-top: 22px;">
                        <div class="quote-title" style="font-size: 22px;">Highlights</div>
                        <ul style="margin: 8px 0 0 0; padding-left: 20px; line-height: 1.8;">
                            @foreach ($highlights_list as $highlight)
                                <li style="font-weight: 600;">{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        @if ($isPdf && !empty($vehicles))
            <div style="page-break-before: always;"></div>
        @endif

        {{-- Vehicles page --}}
        @if (!empty($vehicles))
            <div class="quote-section">
                <div class="quote-body">
                    <div class="quote-title">Our Vehicles</div>
                    <p class="quote-subtitle">Comfortable, custom-fitted safari transport for your journey.</p>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
                        @foreach (collect($vehicles)->chunk(2) as $chunk)
                            <tr>
                                @foreach ($chunk as $vehicle)
                                    <td style="width: 50%; padding: 0 12px 18px 0; vertical-align: top;">
                                        <div class="quote-gallery-card">
                                            @if (!empty($vehicle['image_resolved']))
                                                <div class="quote-gallery-image" @if (!$isPdf) style="background-image: url('{{ $vehicle['image_resolved'] }}');" @endif>
                                                    @if ($isPdf)
                                                        <img src="{{ $vehicle['image_resolved'] }}" alt="{{ $vehicle['name'] }}">
                                                    @endif
                                                </div>
                                            @endif
                                            <div style="padding: 16px;">
                                                <div style="font-weight: 800; font-size: 18px;">{{ $vehicle['name'] }}</div>
                                                @if (!empty($vehicle['capacity']))
                                                    <div style="font-size: 12px; color: #6f7d76; margin: 4px 0;">{{ $vehicle['capacity'] }}</div>
                                                @endif
                                                <div class="quote-subtitle" style="margin-top: 6px;">{{ $vehicle['description'] ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                                @if ($chunk->count() === 1)
                                    <td style="width: 50%;"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif

        @if ($isPdf)
            <div style="page-break-before: always;"></div>
        @endif

        <div class="quote-section">
            <div class="quote-body">
                <table class="quote-summary-table">
                    <tr>
                        <td style="width: 25%; padding-right: 12px;">
                            <div class="quote-summary-box">
                                <span class="label">Route</span>
                                <span class="value">{{ $route_destinations->pluck('name')->filter()->implode(' / ') ?: 'Tailor-made route' }}</span>
                            </div>
                        </td>
                        <td style="width: 25%; padding-right: 12px;">
                            <div class="quote-summary-box">
                                <span class="label">Duration</span>
                                <span class="value">{{ $version->duration_days ?: $days->count() ?: 1 }} Days / {{ $version->duration_nights ?: max(($days->count() ?: 1) - 1, 0) }} Nights</span>
                            </div>
                        </td>
                        <td style="width: 25%; padding-right: 12px;">
                            <div class="quote-summary-box">
                                <span class="label">Service Level</span>
                                <span class="value">{{ $version->serviceClass->name ?? 'Tailor-made' }}</span>
                            </div>
                        </td>
                        <td style="width: 25%;">
                            <div class="quote-summary-box">
                                <span class="label">Budget View</span>
                                <span class="value">
                                    @if ($version->hide_total_price)
                                        Private pricing
                                    @else
                                        {{ $currencyCode }} {{ number_format($version->total_amount ?? 0, 2) }}
                                    @endif
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="quote-section">
            <div class="quote-body">
                <table class="quote-two-col">
                    <tr>
                        <td style="width: 58%; padding-right: 16px; vertical-align: top;">
                            <div class="quote-title">Journey Overview</div>
                            <p class="quote-subtitle">A stronger visual proposal built from your safari library, shared-ready branding, and the current quote version.</p>
                            <p>
                                This proposal combines route flow, destination-led imagery, day-by-day detail, and commercial clarity into one guest-facing presentation. It is designed to feel polished from the first share while staying grounded in your internal quote builder data.
                            </p>

                            @if ($tourist)
                                <p>
                                    <strong>Guest:</strong> {{ $tourist->name }}
                                    @if ($tourist->country)
                                        from {{ $tourist->country->name }}
                                    @endif
                                    @if ($tourist->email)
                                        <br><strong>Email:</strong> {{ $tourist->email }}
                                    @endif
                                </p>
                            @endif

                            @if ($route_destinations->count() > 0)
                                <table style="width: 100%; border-collapse: collapse; margin-top: 18px; font-size: 13px;">
                                    @if ($start_destination)
                                        <tr>
                                            <td style="width: 30%; padding: 8px 12px; font-weight: 800; color: #AC5526;">Start Point</td>
                                            <td style="padding: 8px 12px;">{{ $start_destination }}</td>
                                        </tr>
                                    @endif
                                    @foreach ($days as $day)
                                        <tr style="border-top: 1px solid #e7ece9;">
                                            <td style="width: 30%; padding: 8px 12px; font-weight: 700; color: #AC5526;">Day {{ $day['day_number'] }}</td>
                                            <td style="padding: 8px 12px;">
                                                <strong>{{ optional($day['destination'])->name ?? $day['title'] }}</strong>
                                                @if ($day['accommodation_name'])
                                                    <span style="color: #6f7d76;"> &nbsp;·&nbsp; {{ $day['accommodation_name'] }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($end_destination)
                                        <tr style="border-top: 1px solid #e7ece9;">
                                            <td style="width: 30%; padding: 8px 12px; font-weight: 800; color: #AC5526;">End Point</td>
                                            <td style="padding: 8px 12px;">{{ $end_destination }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @endif
                        </td>
                        <td style="width: 42%; vertical-align: top;">
                            <div class="quote-map-wrap">
                                <div class="quote-title" style="font-size: 18px;">Route Moodboard</div>
                                <p class="quote-subtitle" style="margin-bottom: 14px;">A route-led visual summary driven by the selected destinations and available coordinate data.</p>
                                @include('web.system.quotation.partials.route_map')
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if ($route_destinations->count() > 0)
            @php
                $galleryDestinations = $route_destinations->take(4);
                $singleDestination = $galleryDestinations->count() === 1;
                $cardChunks = $singleDestination ? $galleryDestinations->chunk(1) : $galleryDestinations->chunk(2);
                $cardWidth = $singleDestination ? '100%' : '50%';
            @endphp
            <div class="quote-section">
                <div class="quote-body">
                    <div class="quote-title">Destination Gallery</div>
                    <p class="quote-subtitle">Images are pulled automatically from the destination and trip media library.</p>
                    <table class="quote-summary-table" style="margin-top: 16px;">
                        @foreach ($cardChunks as $chunk)
                            <tr>
                                @foreach ($chunk as $destination)
                                    @php
                                        $destinationDay = $days->first(function ($day) use ($destination) {
                                            return optional($day['destination'])->id === $destination->id;
                                        });
                                        $image = $destinationDay['image'] ?? $hero_image ?? $fallbackImage;
                                    @endphp
                                    <td style="width: {{ $cardWidth }}; padding: 0 {{ $singleDestination ? '0' : '12px' }} 12px 0; vertical-align: top;">
                                        <div class="quote-gallery-card">
                                            <div class="quote-gallery-image" @if (!$isPdf) style="background-image: url('{{ $image }}');" @endif>
                                                @if ($isPdf && $image)
                                                    <img src="{{ $image }}" alt="{{ $destination->name }}">
                                                @endif
                                            </div>
                                            <div style="padding: 16px;">
                                                <div style="font-weight: 800; font-size: 18px;">{{ $destination->name }}</div>
                                                <div class="quote-subtitle">{{ \Illuminate\Support\Str::limit(strip_tags($destination->description ?? ''), 120) ?: 'This stop is part of the curated safari route.' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                                @if (!$singleDestination && $chunk->count() === 1)
                                    <td style="width: 50%;"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif

        @if ($isPdf)
            <div style="page-break-before: always;"></div>
        @endif

        <div class="quote-section">
            <div class="quote-body">
                <div class="quote-title">Day-by-Day Itinerary</div>
                <p class="quote-subtitle">Every day can be refined in the builder, but the layout below is generated automatically for proposal sharing.</p>

                <div style="margin-top: 18px;">
                    @foreach ($days as $day)
                        @php $imageOnRight = $loop->even; @endphp
                        <div class="quote-day-card">
                            <table class="quote-day-table">
                                <tr>
                                    @if (!$imageOnRight)
                                        @php $dayImage = $day['image'] ?: $fallbackImage; @endphp
                                        <td class="quote-day-image" @if (!$isPdf && $dayImage) style="background-image: url('{{ $dayImage }}');" @endif>
                                            @if ($isPdf && !empty($dayImage))
                                                <img src="{{ $dayImage }}" alt="{{ $day['title'] }}">
                                            @endif
                                        </td>
                                    @endif
                                    <td class="quote-day-content">
                                        <div class="quote-day-kicker">Day {{ $day['day_number'] }}</div>
                                        <div class="quote-day-title">{{ $day['title'] }}</div>
                                        <p class="quote-subtitle" style="margin-bottom: 12px;">
                                            {{ $day['destination']->name ?? 'Curated safari route detail' }}
                                            @if ($day['travel_date'])
                                                | {{ optional($day['travel_date'])->format('M d, Y') }}
                                            @endif
                                        </p>

                                        @if ($day['description'])
                                            <p>{{ $day['description'] }}</p>
                                        @else
                                            <p>A curated safari moment shaped around your travel plan, destination flow, and the Dream Travel and Tours library.</p>
                                        @endif

                                        @if ($day['accommodation_name'] || $day['stay_type'])
                                            <p style="margin: 12px 0 10px;">
                                                <strong>Stay:</strong>
                                                {{ $day['accommodation_name'] ?: 'To be confirmed with final lodge selection' }}
                                                @if ($day['stay_type'])
                                                    ({{ $day['stay_type'] }})
                                                @endif
                                                @if ($day['nights'])
                                                    | {{ $day['nights'] }} night{{ $day['nights'] == 1 ? '' : 's' }}
                                                @endif
                                            </p>
                                        @endif

                                        <div style="margin: 0 0 12px;">
                                            @if ($day['breakfast'])
                                                <span class="quote-meal">Breakfast</span>
                                            @endif
                                            @if ($day['lunch'])
                                                <span class="quote-meal">Lunch</span>
                                            @endif
                                            @if ($day['dinner'])
                                                <span class="quote-meal">Dinner</span>
                                            @endif
                                        </div>

                                        @if (collect($day['activities'])->count() > 0)
                                            <div>
                                                <strong>Activities</strong>
                                                <ul class="quote-list" style="margin-top: 8px;">
                                                    @foreach ($day['activities'] as $activity)
                                                        <li>{{ $activity->title }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @if ($day['accommodation_notes'])
                                            <p style="margin-top: 12px;"><strong>Notes:</strong> {{ $day['accommodation_notes'] }}</p>
                                        @endif
                                    </td>
                                    @if ($imageOnRight)
                                        @php $dayImage = $day['image'] ?: $fallbackImage; @endphp
                                        <td class="quote-day-image" @if (!$isPdf && $dayImage) style="background-image: url('{{ $dayImage }}');" @endif>
                                            @if ($isPdf && !empty($dayImage))
                                                <img src="{{ $dayImage }}" alt="{{ $day['title'] }}">
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="quote-section">
            <div class="quote-body">
                <table class="quote-two-col">
                    <tr>
                        <td style="width: 52%; padding-right: 16px; vertical-align: top;">
                            <div class="quote-title">Safari Hosting and Transport</div>
                            <p class="quote-subtitle">A guest-ready summary of how this journey is expected to feel on the ground.</p>
                            <p>
                                {{ $branding['company_name'] }} plans each safari around smooth logistics, strong destination pairing, and service levels that match the chosen travel style.
                                @if ($version->serviceClass)
                                    This proposal is currently aligned to the <strong>{{ $version->serviceClass->name }}</strong> service class.
                                @endif
                            </p>
                            <p>
                                Your itinerary currently covers <strong>{{ $version->duration_days ?: $days->count() ?: 1 }} days</strong> for
                                <strong>{{ $version->guest_count }}</strong> traveler{{ $version->guest_count == 1 ? '' : 's' }} with meal visibility, stay notes, and activity detail ready for final refinement.
                            </p>
                        </td>
                        <td style="width: 48%; vertical-align: top;">
                            <div class="quote-map-wrap">
                                <div class="quote-title" style="font-size: 18px;">Refinement Priorities</div>
                                <ul class="quote-list" style="margin-top: 12px;">
                                    <li>Select final accommodations for overnight stops.</li>
                                    <li>Attach richer activity detail where optional experiences are offered.</li>
                                    <li>Confirm route coordinates for a fuller map presentation.</li>
                                    <li>Use the Seren links below for digital sharing and booking conversion.</li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if (!$version->hide_price_breakdown || !$version->hide_total_price)
            @if ($isPdf)
                <div style="page-break-before: always;"></div>
            @endif
            <div class="quote-section">
                <div class="quote-body">
                    <div class="quote-title">Pricing</div>
                    <p class="quote-subtitle">Built from the current quote version and ready for PDF or digital sharing.</p>

                    {{-- Pricing-page strip mirroring SafariOffice's pricing header --}}
                    <table style="width: 100%; border-collapse: collapse; margin: 14px 0 8px;">
                        <tr>
                            <td style="width: 25%; padding: 8px 12px; background: #f7f3ec; border-radius: 8px;">
                                <span style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #6f7d76;">Tour Length</span>
                                <div style="font-weight: 800;">{{ $version->duration_days ?: $days->count() ?: 1 }} Days / {{ $version->duration_nights ?: max(($days->count() ?: 1) - 1, 0) }} Nights</div>
                            </td>
                            <td style="width: 25%; padding: 8px 12px; background: #f7f3ec; border-radius: 8px;">
                                <span style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #6f7d76;">Travelers</span>
                                <div style="font-weight: 800;">{{ $version->guest_count }} {{ $version->guest_count == 1 ? 'Adult' : 'Adults' }}</div>
                            </td>
                            <td style="width: 25%; padding: 8px 12px; background: #f7f3ec; border-radius: 8px;">
                                <span style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #6f7d76;">Start Tour</span>
                                <div style="font-weight: 800;">{{ optional($version->start_date)->format('F j, Y') ?? 'TBD' }}</div>
                            </td>
                            <td style="width: 25%; padding: 8px 12px; background: #f7f3ec; border-radius: 8px;">
                                <span style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #6f7d76;">End Tour</span>
                                <div style="font-weight: 800;">{{ optional($version->end_date)->format('F j, Y') ?? 'TBD' }}</div>
                            </td>
                        </tr>
                    </table>

                    @if (!$version->hide_price_breakdown)
                        <table class="quote-price-table" style="margin-top: 18px;">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($price_lines as $line)
                                    <tr>
                                        <td>{{ $line->description }}</td>
                                        <td>{{ $line->traveler_type ?: ($line->is_optional ? 'Optional' : 'Standard') }}</td>
                                        <td>{{ $line->quantity }}</td>
                                        <td>{{ $line->currency->short_name ?? $currencyCode }} {{ number_format($line->unit_price ?? 0, 2) }}</td>
                                        <td>{{ $line->currency->short_name ?? $currencyCode }} {{ number_format($line->total_price ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Pricing is being prepared.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif

                    @if (!$version->hide_total_price)
                        <div class="quote-price-highlight" style="margin-top: 20px; {{ $brandedBackdropStyle }}">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 65%; vertical-align: top;">
                                        <div style="font-size: 24px; font-weight: 800;">Estimated Trip Total</div>
                                        <div style="opacity: 0.85;">This total reflects visible non-optional lines plus VAT.</div>
                                    </td>
                                    <td style="width: 35%; text-align: right; vertical-align: top;">
                                        <div style="font-size: 14px; opacity: 0.8;">Subtotal {{ $currencyCode }} {{ number_format($version->amount ?? 0, 2) }}</div>
                                        <div style="font-size: 14px; opacity: 0.8;">VAT {{ $currencyCode }} {{ number_format($version->vat_amount ?? 0, 2) }}</div>
                                        <div style="font-size: 30px; font-weight: 800; margin-top: 6px;">{{ $currencyCode }} {{ number_format($version->total_amount ?? 0, 2) }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if ((!$version->hide_terms && ($included_terms->count() > 0 || $excluded_terms->count() > 0)) || (!$version->hide_payment_terms && $payment_terms->count() > 0))
            <div class="quote-section">
                <div class="quote-body">
                    <div class="quote-title">Commercial Terms</div>
                    <p class="quote-subtitle">Clear guest-facing inclusions, exclusions, and payment notes.</p>

                    <table class="quote-two-col" style="margin-top: 16px;">
                        <tr>
                            <td style="width: 50%; padding-right: 14px; vertical-align: top;">
                                @if (!$version->hide_terms && $included_terms->count() > 0)
                                    <div class="quote-map-wrap">
                                        <div class="quote-title" style="font-size: 18px;">Included</div>
                                        <ul class="quote-list" style="margin-top: 12px;">
                                            @foreach ($included_terms as $term)
                                                <li>{{ $term->description }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                @if (!$version->hide_terms && $excluded_terms->count() > 0)
                                    <div class="quote-map-wrap">
                                        <div class="quote-title" style="font-size: 18px;">Excluded</div>
                                        <ul class="quote-list" style="margin-top: 12px;">
                                            @foreach ($excluded_terms as $term)
                                                <li>{{ $term->description }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if (!$version->hide_payment_terms && $payment_terms->count() > 0)
                        <div class="quote-map-wrap" style="margin-top: 18px;">
                            <div class="quote-title" style="font-size: 18px;">Payment Terms</div>
                            <ul class="quote-list" style="margin-top: 12px;">
                                @foreach ($payment_terms as $term)
                                    <li>
                                        @if ($term->title)
                                            <strong>{{ $term->title }}:</strong>
                                        @endif
                                        {{ $term->description }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if ($isPdf)
            <div style="page-break-before: always;"></div>
        @endif

        <div class="quote-section quote-company" style="{{ $brandedBackdropStyle }}">
            <div class="quote-body">
                <table class="quote-two-col">
                    <tr>
                        <td style="width: 62%; padding-right: 20px; vertical-align: top;">
                            @if ($displayLogo)
                                <img src="{{ $displayLogo }}" alt="{{ $branding['company_name'] }}" class="quote-cover-logo" style="margin-bottom: 14px;">
                            @endif
                            <div class="quote-title">About {{ $branding['company_name'] }}</div>
                            <p class="quote-subtitle" style="opacity: 0.85;">A proposal that feels complete from the first share, backed by your own destination and trip library.</p>
                            <p style="margin-top: 14px;">{{ $company_profile }}</p>

                            @if (!empty($company_mission))
                                <div style="margin-top: 18px;">
                                    <div style="font-size: 16px; font-weight: 800;">Our Mission</div>
                                    <p style="margin-top: 6px;">{{ $company_mission }}</p>
                                </div>
                            @endif

                            @if (!empty($company_vision))
                                <div style="margin-top: 14px;">
                                    <div style="font-size: 16px; font-weight: 800;">Our Vision</div>
                                    <p style="margin-top: 6px;">{{ $company_vision }}</p>
                                </div>
                            @endif
                        </td>
                        <td style="width: 38%; vertical-align: top;">
                            <div style="background: rgba(255,255,255,0.1); border-radius: 18px; padding: 18px;">
                                <div style="font-size: 18px; font-weight: 800; margin-bottom: 10px;">Contact Us</div>
                                @if (!empty($company_address))
                                    <p style="margin: 0 0 8px;"><strong>Address</strong> &nbsp; {{ $company_address }}</p>
                                @endif
                                @if (!empty($company_country))
                                    <p style="margin: 0 0 8px;"><strong>Country</strong> &nbsp; {{ $company_country }}</p>
                                @endif
                                @if (!empty($company_email))
                                    <p style="margin: 0 0 8px;"><strong>Email</strong> &nbsp; {{ $company_email }}</p>
                                @endif
                                @if (!empty($website_url))
                                    <p style="margin: 0 0 8px;"><strong>Website</strong> &nbsp; <a href="{{ $website_url }}">{{ $website_url }}</a></p>
                                @endif
                                @if (!empty($company_phone))
                                    <p style="margin: 0;"><strong>Phone</strong> &nbsp; {{ $company_phone }}</p>
                                @endif
                            </div>

                            @php
                                $primaryCtaStyle = 'display: inline-block; margin: 0 8px 8px 0; padding: 10px 16px; border-radius: 999px; background: #0097DC; color: #fff; text-decoration: none; font-weight: 800; border: none; cursor: pointer; font-family: inherit;';
                                $secondaryCtaStyle = 'display: inline-block; margin: 0 0 8px 0; padding: 10px 16px; border-radius: 999px; background: rgba(255,255,255,0.14); color: #fff; text-decoration: none; font-weight: 800; border: 1px solid rgba(255,255,255,0.28); cursor: pointer; font-family: inherit;';
                            @endphp
                            <div style="background: rgba(255,255,255,0.1); border-radius: 18px; padding: 18px; margin-top: 16px;">
                                <div style="font-size: 18px; font-weight: 800; margin-bottom: 10px;">Ready to Confirm?</div>
                                <p style="margin: 0 0 12px;">Continue the client journey online through {{ $branding['company_name'] }}.</p>
                                @if ($canBookFromQuote)
                                    @if ($inlineBookingModal)
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#quoteBookingModal" style="{{ $primaryCtaStyle }}">Book This Safari</button>
                                    @elseif ($publicMode)
                                        <a href="{{ route('public.itinerary.book.confirm', $version->public_token) }}" style="{{ $primaryCtaStyle }}">Book This Safari</a>
                                    @elseif ($public_booking_url)
                                        <a href="{{ $public_booking_url }}" style="{{ $primaryCtaStyle }}">Book This Safari</a>
                                    @endif
                                @endif
                                @if ($website_url)
                                    <a href="{{ $website_url }}" target="_blank" rel="noopener" style="{{ $secondaryCtaStyle }}">Visit Our Website</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if ($isPdf)
            <div style="page-break-before: always;"></div>
        @endif

        <!-- {{-- Colofon page --}}
        @if (!empty($colofon))
            <div class="quote-section" style="background: #AC5526; color: #fff; min-height: 540px;">
                <div class="quote-body" style="padding: 60px 40px;">
                    @if (!empty($colofon['quote']))
                        <div style="font-family: Georgia, 'Times New Roman', serif; font-size: 36px; line-height: 1.25; margin-top: 80px;">
                            &ldquo;{{ $colofon['quote'] }}&rdquo;
                        </div>
                        @if (!empty($colofon['quote_author']))
                            <div style="margin-top: 18px; font-size: 16px; opacity: 0.85;">— {{ $colofon['quote_author'] }}</div>
                        @endif
                    @endif

                    <div style="margin-top: 80px; border-top: 1px solid rgba(255,255,255,0.18); padding-top: 18px;">
                        <div style="font-size: 16px; font-weight: 800; margin-bottom: 10px;">Colofon</div>
                        @if (!empty($colofon['copyright_text']))
                            <p style="margin: 0 0 6px;"><strong>Copyright Text</strong> &nbsp; {{ $colofon['copyright_text'] }}</p>
                        @endif
                        @if (!empty($colofon['copyright_images']))
                            <p style="margin: 0 0 6px;"><strong>Copyright Images</strong> &nbsp; {{ $colofon['copyright_images'] }}</p>
                        @endif
                        <p style="margin-top: 16px; font-size: 12px; opacity: 0.75;">
                            {{ $agent['name'] ?? $branding['company_name'] }} prepared this proposal for {{ $tourist->name ?? 'you' }}.
                        </p>
                    </div>
                </div>
            </div>
        @endif -->

        <div class="quote-footer-bar">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 58%; vertical-align: middle;">
                        @if ($displayLogo)
                            <img src="{{ $displayLogo }}" alt="{{ $branding['company_name'] }}" class="quote-brand-logo" style="max-height: 34px; max-width: 120px;">
                        @else
                            <strong>{{ $branding['company_name'] }}</strong>
                        @endif
                        <div style="font-size: 13px; color: #61736c; margin-top: 8px;">{{ $branding['tagline'] }}</div>
                    </td>
                    <td style="width: 42%; text-align: right; vertical-align: middle;">
                        <div style="font-size: 13px; margin-bottom: 6px;">
                            <a href="{{ $website_url }}">{{ $branding['powered_by_label'] }}</a>
                        </div>
                        <div style="font-size: 12px; color: #61736c;">Generated {{ $generatedLabel }} | {{ $document_reference }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
