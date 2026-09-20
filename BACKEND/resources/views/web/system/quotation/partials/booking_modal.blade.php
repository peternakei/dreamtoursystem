@php
    $touristModel = $tourist ?? null;
    $touristName = trim((string) (optional($touristModel)->name ?? ''));
    $touristEmail = (string) (optional($touristModel)->email ?? '');
    $touristPhone = (string) (optional($touristModel)->phone ?? '');
    $modalCurrencyCode = $version->currency->short_name ?? 'USD';
    $modalCoverImage = $hero_image ?? null;
    $modalGuestCount = $version->guest_count ?: 1;
    $modalStartLabel = optional($version->start_date)->format('F j, Y') ?? 'TBD';
    $modalEndLabel = optional($version->end_date)->format('F j, Y') ?? 'TBD';
    $modalStartDateValue = optional($version->start_date)->format('Y-m-d') ?? '';
    $modalTotalAmount = (float) ($version->total_amount ?? 0);
    $modalPerGuest = $modalGuestCount > 0 ? $modalTotalAmount / $modalGuestCount : $modalTotalAmount;
    $publicPdfUrl = $public_pdf_url ?? null;
    // Caller can override which endpoint the modal POSTs to. Defaults to the
    // internal (auth-protected) endpoint; the public itinerary page passes
    // the public booking route so unauthenticated guests can book too.
    $bookingEndpoint = $bookingEndpoint ?? route('quotation_versions.book', $version->uuid);
@endphp

<style>
    /* Brand-themed booking modal — light surface with brand orange (#AC5526)
       as the primary accent and brand blue (#0097DC) for info touches. */
    .quote-booking-modal .modal-dialog {
        max-width: 780px;
    }

    .quote-booking-modal .modal-content {
        background: #ffffff;
        color: #2A2620;
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
        box-shadow: 0 20px 50px rgba(44, 33, 22, 0.18);
    }

    /* Brand gradient header strip — orange → blue, mirrors the proposal cover. */
    .quote-booking-modal .modal-header {
        border-bottom: 0;
        padding: 18px 26px;
        background: linear-gradient(135deg, #AC5526 0%, #0097DC 100%);
        color: #fff;
    }

    .quote-booking-modal .modal-header .modal-title {
        font-weight: 700;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quote-booking-modal .modal-header .modal-title::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.18);
    }

    .quote-booking-modal .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(2);
        opacity: 0.85;
    }

    .quote-booking-modal .modal-body {
        padding: 26px 30px 30px;
        background: #FFFCF7;
    }

    /* Stepper — orange for the active step. */
    .quote-booking-modal .qb-stepper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .quote-booking-modal .qb-step {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #A89A8B;
    }

    .quote-booking-modal .qb-step.active {
        color: #AC5526;
        font-weight: 700;
    }

    .quote-booking-modal .qb-step .qb-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: transparent;
        border: 1.5px solid currentColor;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    .quote-booking-modal .qb-step.active .qb-dot {
        background: #AC5526;
        color: #fff;
        border-color: #AC5526;
    }

    .quote-booking-modal .qb-bar {
        width: 60px;
        height: 1px;
        background: #E5DACA;
    }

    .quote-booking-modal .qb-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 28px;
        font-weight: 800;
        color: #2A2620;
        margin: 8px 0 6px;
    }

    .quote-booking-modal .qb-lead {
        color: #7C6E60;
        font-size: 13.5px;
        margin: 0 0 22px;
        line-height: 1.55;
    }

    .quote-booking-modal .qb-section-title {
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #AC5526;
        margin-bottom: 10px;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
    }

    .quote-booking-modal .qb-ref {
        font-size: 12px;
        color: #7C6E60;
        font-weight: 400;
        text-transform: none;
        letter-spacing: 0;
    }

    .quote-booking-modal .qb-ref span { color: #0097DC; font-weight: 600; }

    /* Tour overview — soft tinted card with brand stripe on the left. */
    .quote-booking-modal .qb-tour-card {
        background: linear-gradient(135deg, rgba(172, 85, 38, 0.05), rgba(0, 151, 220, 0.04));
        border: 1px solid #EAE0CF;
        border-left: 4px solid #AC5526;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: grid;
        grid-template-columns: 86px 1fr;
        gap: 16px;
        align-items: start;
    }

    .quote-booking-modal .qb-thumb {
        width: 86px;
        height: 86px;
        border-radius: 10px;
        background: linear-gradient(135deg, #AC5526, #0097DC);
        background-size: cover;
        background-position: center;
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.4);
    }

    .quote-booking-modal .qb-duration {
        display: inline-block;
        font-size: 11.5px;
        font-weight: 700;
        color: #AC5526;
        background: rgba(172, 85, 38, 0.1);
        padding: 3px 10px;
        border-radius: 999px;
        margin-bottom: 6px;
        letter-spacing: 0.04em;
    }

    .quote-booking-modal .qb-tour-name {
        margin: 0 0 12px;
        font-size: 19px;
        font-weight: 800;
        color: #2A2620;
        line-height: 1.25;
    }

    .quote-booking-modal .qb-tour-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 24px;
        font-size: 12.5px;
    }

    .quote-booking-modal .qb-k {
        color: #0097DC;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        display: block;
        margin-bottom: 2px;
    }

    /* Cost breakdown — clean white table with orange total. */
    .quote-booking-modal .qb-breakdown {
        border: 1px solid #EAE0CF;
        background: #fff;
        border-radius: 10px;
        display: grid;
        grid-template-columns: 1fr auto auto;
        overflow: hidden;
        font-size: 13.5px;
        margin-bottom: 6px;
    }

    .quote-booking-modal .qb-breakdown .qb-cell {
        padding: 13px 14px;
    }

    .quote-booking-modal .qb-breakdown .qb-qty { text-align: right; color: #7C6E60; }
    .quote-booking-modal .qb-breakdown .qb-total {
        text-align: right;
        font-weight: 700;
        color: #2A2620;
        background: rgba(172, 85, 38, 0.06);
        border-left: 1px solid #EAE0CF;
    }

    .quote-booking-modal .qb-grand {
        text-align: right;
        margin-bottom: 18px;
        font-size: 13px;
        color: #7C6E60;
    }

    .quote-booking-modal .qb-grand strong {
        color: #AC5526;
        font-size: 16px;
    }

    /* Accordions — pill cards. */
    .quote-booking-modal details.qb-acc {
        border: 1px solid #EAE0CF;
        background: #fff;
        border-radius: 10px;
        margin: 10px 0;
        overflow: hidden;
        transition: border-color 0.15s ease;
    }

    .quote-booking-modal details.qb-acc[open] {
        border-color: #AC5526;
    }

    .quote-booking-modal details.qb-acc summary {
        list-style: none;
        cursor: pointer;
        padding: 13px 16px;
        font-weight: 600;
        color: #2A2620;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13.5px;
    }

    .quote-booking-modal details.qb-acc summary::-webkit-details-marker { display: none; }
    .quote-booking-modal details.qb-acc summary::after {
        content: '\25BE';
        color: #AC5526;
        transition: transform 0.15s ease;
    }
    .quote-booking-modal details.qb-acc[open] summary::after { transform: rotate(180deg); }

    .quote-booking-modal details.qb-acc .qb-acc-body {
        padding: 0 16px 14px;
        font-size: 13px;
        color: #5C4F42;
    }

    .quote-booking-modal details.qb-acc .qb-acc-body ul {
        margin: 0;
        padding-left: 18px;
    }

    .quote-booking-modal details.qb-acc .qb-acc-body li { margin-bottom: 6px; }

    /* Personal Information form — bordered table with focus-glow inputs. */
    .quote-booking-modal .qb-form-title {
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #AC5526;
        margin: 26px 0 10px;
    }

    .quote-booking-modal .qb-form-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #EAE0CF;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
    }

    .quote-booking-modal .qb-form-table tr + tr td { border-top: 1px solid #F0E6D5; }

    .quote-booking-modal .qb-form-table td {
        padding: 12px 14px;
        font-size: 13.5px;
        vertical-align: middle;
    }

    .quote-booking-modal .qb-form-table td.qb-label {
        width: 36%;
        color: #2A2620;
        font-weight: 600;
        background: #FBF6EC;
    }

    .quote-booking-modal .qb-form-table td.qb-input input {
        width: 100%;
        background: transparent;
        border: 0;
        color: #2A2620;
        font-family: inherit;
        font-size: 13.5px;
        padding: 0;
        outline: none;
    }

    .quote-booking-modal .qb-form-table td.qb-input input::placeholder { color: #B5A797; }

    .quote-booking-modal .qb-form-table tr:focus-within td.qb-label { color: #AC5526; }

    /* Optional message reveal — blue accent. */
    .quote-booking-modal .qb-message {
        margin-top: 14px;
    }

    .quote-booking-modal .qb-message summary {
        list-style: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #0097DC;
        font-size: 13.5px;
        font-weight: 600;
    }

    .quote-booking-modal .qb-message summary::-webkit-details-marker { display: none; }

    .quote-booking-modal .qb-message summary::before {
        content: '+';
        display: inline-block;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1px solid currentColor;
        line-height: 16px;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
    }

    .quote-booking-modal .qb-message[open] summary::before { content: '-'; }

    .quote-booking-modal .qb-opt {
        color: #A89A8B;
        font-size: 12px;
        margin-left: 6px;
        font-weight: 400;
    }

    .quote-booking-modal .qb-message textarea {
        width: 100%;
        min-height: 86px;
        margin-top: 10px;
        background: #fff;
        border: 1px solid #EAE0CF;
        border-radius: 10px;
        padding: 10px 12px;
        color: #2A2620;
        font-family: inherit;
        font-size: 13.5px;
        resize: vertical;
        transition: border-color 0.15s ease;
    }

    .quote-booking-modal .qb-message textarea:focus {
        border-color: #0097DC;
        outline: none;
    }

    /* Footer — brand-orange Confirm button + blue PDF link. */
    .quote-booking-modal .qb-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .quote-booking-modal .qb-confirm {
        background: #AC5526;
        color: #fff;
        border: 0;
        padding: 12px 26px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 13.5px;
        cursor: pointer;
        font-family: inherit;
        letter-spacing: 0.02em;
        min-width: 180px;
        box-shadow: 0 6px 14px rgba(172, 85, 38, 0.25);
        transition: background 0.15s ease, transform 0.1s ease;
    }

    .quote-booking-modal .qb-confirm:hover { background: #8E441E; }
    .quote-booking-modal .qb-confirm:active { transform: translateY(1px); }
    .quote-booking-modal .qb-confirm:disabled { opacity: 0.7; cursor: progress; }

    .quote-booking-modal .qb-pdf-link {
        color: #0097DC;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .quote-booking-modal .qb-pdf-link:hover { text-decoration: underline; }
    .quote-booking-modal .qb-pdf-link::before { content: '\1F4C4'; }

    .quote-booking-modal .qb-disclaimer {
        color: #7C6E60;
        font-size: 12px;
        margin-top: 14px;
        line-height: 1.6;
    }

    .quote-booking-modal .qb-error {
        background: #FBEAE8;
        border: 1px solid #F0BFB9;
        color: #9C2F26;
        padding: 12px 14px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13.5px;
        display: none;
    }

    .quote-booking-modal .qb-error.is-on { display: block; }

    /* Success state — brand celebratory panel. */
    .quote-booking-modal .qb-success {
        text-align: center;
        padding: 36px 20px 22px;
        background:
            radial-gradient(circle at top, rgba(172, 85, 38, 0.1), transparent 60%),
            radial-gradient(circle at bottom, rgba(0, 151, 220, 0.08), transparent 60%);
        border-radius: 14px;
    }

    .quote-booking-modal .qb-success .qb-tick {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #AC5526, #0097DC);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 16px;
        box-shadow: 0 10px 24px rgba(172, 85, 38, 0.25);
    }

    .quote-booking-modal .qb-success-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 26px;
        font-weight: 800;
        color: #2A2620;
        margin: 0 0 8px;
    }

    .quote-booking-modal .qb-success-body {
        color: #5C4F42;
        font-size: 14px;
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .quote-booking-modal .qb-success-ref {
        display: inline-block;
        margin-top: 6px;
        padding: 6px 14px;
        background: rgba(0, 151, 220, 0.1);
        color: #0097DC;
        border-radius: 999px;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.04em;
    }

    .quote-booking-modal .qb-success-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 22px;
    }

    .quote-booking-modal .qb-success-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
        font-family: inherit;
    }

    .quote-booking-modal .qb-success-btn.primary {
        background: #AC5526;
        color: #fff;
        border: 1px solid #AC5526;
        box-shadow: 0 6px 14px rgba(172, 85, 38, 0.22);
    }

    .quote-booking-modal .qb-success-btn.primary:hover { background: #8E441E; border-color: #8E441E; }

    .quote-booking-modal .qb-success-btn.outline {
        background: transparent;
        color: #0097DC;
        border: 1px solid #0097DC;
    }

    .quote-booking-modal .qb-success-btn.outline:hover { background: rgba(0, 151, 220, 0.08); }
</style>

<div class="modal fade quote-booking-modal" id="quoteBookingModal" tabindex="-1" aria-labelledby="quoteBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteBookingModalLabel">Confirm Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="qb-form-pane">
                    <div class="qb-stepper">
                        <span class="qb-step active"><span class="qb-dot">1</span> Check &amp; confirm your booking</span>
                        <span class="qb-bar"></span>
                        <span class="qb-step"><span class="qb-dot">2</span> We confirm details</span>
                    </div>

                    <h2 class="qb-title">Confirm Your Booking</h2>
                    <p class="qb-lead">
                        Use the form below to confirm this booking. After confirmation we will start securing accommodation, activities and options for the tour. The guest will receive a confirmation with further details.
                    </p>

                    <div class="qb-error" id="qb-error"></div>

                    <div class="qb-section-title">
                        <span>Overview Tour</span>
                        <span class="qb-ref">Ref. Number: <span>#{{ $document_reference }}</span></span>
                    </div>

                    <div class="qb-tour-card">
                        <div class="qb-thumb" @if ($modalCoverImage) style="background-image: url('{{ $modalCoverImage }}');" @endif></div>
                        <div>
                            <div class="qb-duration">{{ $version->duration_days ?: 1 }} Days / {{ $version->duration_nights ?: 0 }} Nights</div>
                            <div class="qb-tour-name">{{ $version->title }}</div>
                            <div class="qb-tour-meta">
                                <div>
                                    <span class="qb-k">Start Tour</span>
                                    <span>{{ $modalStartLabel }}</span>
                                </div>
                                <div>
                                    <span class="qb-k">End Tour</span>
                                    <span>{{ $modalEndLabel }}</span>
                                </div>
                                <div>
                                    <span class="qb-k">Start Destination</span>
                                    <span>{{ $start_destination ?: '—' }}</span>
                                </div>
                                <div>
                                    <span class="qb-k">End Destination</span>
                                    <span>{{ $end_destination ?: '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="qb-section-title"><span>Breakdown of Costs</span></div>
                    <div class="qb-breakdown">
                        <div class="qb-cell">{{ $modalGuestCount }}x Adult</div>
                        <div class="qb-cell qb-qty">{{ $modalGuestCount }}x {{ $modalCurrencyCode }} {{ number_format($modalPerGuest, 2) }}</div>
                        <div class="qb-cell qb-total">{{ $modalCurrencyCode }} {{ number_format($modalTotalAmount, 2) }}</div>
                    </div>
                    <div class="qb-grand">Total in {{ $modalCurrencyCode }}: <strong>{{ $modalCurrencyCode }} {{ number_format($modalTotalAmount, 2) }}</strong></div>

                    <details class="qb-acc">
                        <summary>What is included?</summary>
                        <div class="qb-acc-body">
                            @if ($included_terms->count() > 0)
                                <ul>
                                    @foreach ($included_terms as $term)
                                        <li>{{ $term->description }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p style="margin: 0;">Inclusion details are listed in the full quotation.</p>
                            @endif

                            @if ($excluded_terms->count() > 0)
                                <p style="margin: 14px 0 6px; color: #2A2620; font-weight: 700;">Excluded</p>
                                <ul>
                                    @foreach ($excluded_terms as $term)
                                        <li>{{ $term->description }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </details>

                    <details class="qb-acc">
                        <summary>What are the payment &amp; booking terms?</summary>
                        <div class="qb-acc-body">
                            @if ($payment_terms->count() > 0)
                                <ul>
                                    @foreach ($payment_terms as $term)
                                        <li>
                                            @if ($term->title)<strong style="color: #2A2620;">{{ $term->title }}:</strong> @endif
                                            {{ $term->description }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p style="margin: 0;">Payment and booking terms will be confirmed by the consultant.</p>
                            @endif
                        </div>
                    </details>

                    <form id="qb-booking-form" autocomplete="off">
                        @csrf
                        <input type="hidden" name="lname" value="">
                        <input type="hidden" name="phone" value="{{ $touristPhone }}">
                        <input type="hidden" name="guest_count" value="{{ $modalGuestCount }}">
                        <input type="hidden" name="start_date" value="{{ $modalStartDateValue }}">

                        <div class="qb-form-title">Personal Information</div>
                        <table class="qb-form-table">
                            <tr>
                                <td class="qb-label">Name</td>
                                <td class="qb-input">
                                    <input name="fname" required value="{{ $touristName }}" placeholder="Mr./Mrs. Full Name">
                                </td>
                            </tr>
                            <tr>
                                <td class="qb-label">Email Address</td>
                                <td class="qb-input">
                                    <input type="email" name="email" required value="{{ $touristEmail }}" placeholder="guest@email.com">
                                </td>
                            </tr>
                            @if ($touristPhone)
                                <tr>
                                    <td class="qb-label">Phone</td>
                                    <td class="qb-input">
                                        <input type="text" name="phone_display" value="{{ $touristPhone }}" placeholder="+255 …" oninput="document.querySelector('input[name=phone]').value = this.value">
                                    </td>
                                </tr>
                            @endif
                        </table>

                        <details class="qb-message">
                            <summary>Add Message <span class="qb-opt">Optional</span></summary>
                            <textarea name="note" placeholder="Anything we should know? Dietary needs, arrival flight, special requests…"></textarea>
                        </details>

                        <div class="qb-footer">
                            <button type="submit" class="qb-confirm" id="qb-submit">Confirm Booking</button>
                            @if ($publicPdfUrl)
                                <a class="qb-pdf-link" href="{{ $publicPdfUrl }}" target="_blank" rel="noopener">View Detailed Quote — PDF</a>
                            @endif
                        </div>

                        <p class="qb-disclaimer">
                            Clicking the Confirm Booking button does not take the guest to payment.<br>
                            It only informs us to start the booking process and secure reservations.
                        </p>
                    </form>
                </div>

                <div id="qb-success-pane" style="display:none;">
                    <div class="qb-success">
                        <div class="qb-tick">&#10003;</div>
                        <h3 class="qb-success-title">Booking Confirmed</h3>
                        <p class="qb-success-body">
                            The booking for <strong>{{ $version->title }}</strong> has been recorded.
                            <span class="qb-success-ref" id="qb-success-ref"></span>
                        </p>
                        <div class="qb-success-actions">
                            @if ($version->public_url_enabled && $version->public_token)
                                <a href="{{ route('public.itinerary.show', $version->public_token) }}" target="_blank" rel="noopener" class="qb-success-btn primary">View Digital Itinerary</a>
                            @endif
                            <a href="{{ route('quotation_versions.pdf.download', $version->uuid) }}" class="qb-success-btn outline">Download Quote PDF</a>
                            <button type="button" class="qb-success-btn outline" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var form = document.getElementById('qb-booking-form');
        var submitBtn = document.getElementById('qb-submit');
        var errorBox = document.getElementById('qb-error');
        var formPane = document.getElementById('qb-form-pane');
        var successPane = document.getElementById('qb-success-pane');
        var successRef = document.getElementById('qb-success-ref');
        var endpoint = @json($bookingEndpoint);

        if (!form) return;

        function showError(message) {
            if (!errorBox) return;
            errorBox.textContent = message || 'Booking could not be saved. Please try again.';
            errorBox.classList.add('is-on');
        }

        function clearError() {
            if (!errorBox) return;
            errorBox.textContent = '';
            errorBox.classList.remove('is-on');
        }

        function showSuccess(reference) {
            if (formPane) formPane.style.display = 'none';
            if (successPane) successPane.style.display = 'block';
            if (successRef && reference) {
                successRef.textContent = '#' + reference;
            } else if (successRef) {
                successRef.style.display = 'none';
            }
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearError();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Confirming…';

            var formData = new FormData(form);

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json().then(function (payload) {
                    return { status: response.status, payload: payload };
                });
            }).then(function (result) {
                if (result.status >= 200 && result.status < 300 && result.payload && result.payload.ok) {
                    showSuccess(result.payload.booking_reference || '');
                    return;
                }
                var msg = (result.payload && (result.payload.message || (result.payload.errors && Object.values(result.payload.errors).flat().join(' ')))) || 'Booking could not be saved.';
                showError(msg);
            }).catch(function () {
                showError('Network error. Please try again.');
            }).finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Confirm Booking';
            });
        });
    })();
</script>
