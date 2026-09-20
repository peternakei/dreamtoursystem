<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Confirm Your Booking — {{ $document_reference }}</title>
    <style>
        :root {
            --bg: #0b1411;
            --panel: #11201b;
            --panel-2: #15282221;
            --line: #1f3a32;
            --muted: #9bb0aa;
            --text: #e8efec;
            --gold: #c9862f;
            --green: #4caf50;
            --green-deep: #0f5d4b;
            --green-soft: #74b87a;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--text);
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            line-height: 1.55;
        }

        a { color: inherit; }

        .topbar {
            border-bottom: 1px solid var(--line);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar img {
            max-height: 44px;
            max-width: 120px;
        }

        .topbar .agent {
            text-align: right;
            font-size: 13px;
        }

        .topbar .agent strong {
            display: block;
            font-weight: 700;
        }

        .topbar .agent a {
            color: var(--green-soft);
            text-decoration: underline;
            font-size: 12px;
        }

        .shell {
            max-width: 760px;
            margin: 0 auto;
            padding: 36px 22px 80px;
        }

        .stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .stepper .step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .stepper .step.active {
            color: var(--green-soft);
            font-weight: 700;
        }

        .stepper .dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1.5px solid currentColor;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .stepper .bar {
            width: 60px;
            height: 1px;
            background: var(--line);
        }

        h1.page-title {
            font-size: 34px;
            font-weight: 800;
            margin: 14px 0 10px;
            font-family: Georgia, 'Times New Roman', serif;
        }

        .lead {
            color: var(--muted);
            margin: 0 0 28px;
        }

        .errors {
            background: rgba(179, 36, 36, 0.12);
            border: 1px solid rgba(179, 36, 36, 0.45);
            color: #ffb4b4;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        .section-title .ref {
            font-size: 12px;
            color: var(--muted);
            font-weight: 400;
        }

        .section-title .ref a {
            color: var(--green-soft);
            text-decoration: none;
        }

        .tour-card {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            padding: 18px 0;
            margin-bottom: 26px;
            display: grid;
            grid-template-columns: 90px 1fr;
            gap: 18px;
            align-items: start;
        }

        .tour-card .thumb {
            width: 90px;
            height: 90px;
            border-radius: 8px;
            background: var(--green-deep);
            background-size: cover;
            background-position: center;
        }

        .tour-card .duration {
            font-size: 13px;
            color: var(--muted);
        }

        .tour-card h3 {
            margin: 4px 0 14px;
            font-size: 22px;
            font-weight: 800;
        }

        .tour-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
            font-size: 13px;
        }

        .tour-meta .k {
            color: var(--green-soft);
            font-size: 12px;
            display: block;
            margin-bottom: 2px;
        }

        .tour-meta .v {
            color: var(--text);
        }

        .breakdown-title {
            font-weight: 700;
            margin: 0 0 10px;
        }

        .breakdown {
            border: 1px solid var(--line);
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr auto auto;
            overflow: hidden;
        }

        .breakdown .cell {
            padding: 14px 16px;
            border-top: 1px solid var(--line);
            font-size: 14px;
        }

        .breakdown .cell:nth-child(-n+3) {
            border-top: 0;
        }

        .breakdown .label { color: var(--text); }
        .breakdown .qty { text-align: right; color: var(--muted); }
        .breakdown .total { text-align: right; font-weight: 700; border-left: 1px solid var(--line); }

        .breakdown-total {
            text-align: right;
            margin-top: 10px;
            font-size: 14px;
        }

        .breakdown-total strong {
            color: var(--gold);
            font-size: 16px;
        }

        .accordion {
            border: 1px solid var(--line);
            border-radius: 8px;
            margin: 12px 0;
            overflow: hidden;
        }

        .accordion summary {
            list-style: none;
            cursor: pointer;
            padding: 14px 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .accordion summary::-webkit-details-marker { display: none; }

        .accordion summary::after {
            content: '\25BE';
            color: var(--muted);
            transition: transform 0.15s ease;
        }

        .accordion[open] summary::after {
            transform: rotate(180deg);
        }

        .accordion .body {
            padding: 0 16px 16px;
            font-size: 14px;
            color: var(--muted);
        }

        .accordion .body ul {
            margin: 0;
            padding-left: 18px;
        }

        .accordion .body li {
            margin-bottom: 6px;
        }

        .form-section-title {
            font-weight: 700;
            margin: 32px 0 12px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }

        .form-table tr + tr td { border-top: 1px solid var(--line); }
        .form-table td {
            padding: 14px 16px;
            font-size: 14px;
            vertical-align: middle;
        }

        .form-table td.label {
            width: 36%;
            color: var(--text);
            font-weight: 600;
        }

        .form-table td.input input {
            width: 100%;
            background: transparent;
            border: 0;
            color: var(--text);
            font-family: inherit;
            font-size: 14px;
            padding: 0;
            outline: none;
        }

        .form-table td.input input::placeholder { color: var(--muted); }

        .add-message {
            margin-top: 14px;
        }

        .add-message summary {
            list-style: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--green-soft);
            font-size: 14px;
        }

        .add-message summary::-webkit-details-marker { display: none; }

        .add-message summary::before {
            content: '+';
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1px solid currentColor;
            line-height: 16px;
            text-align: center;
            font-weight: 700;
        }

        .add-message[open] summary::before { content: '-'; }

        .add-message .opt {
            color: var(--muted);
            font-size: 12px;
            margin-left: 6px;
        }

        .add-message textarea {
            width: 100%;
            min-height: 96px;
            margin-top: 10px;
            background: transparent;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px 14px;
            color: var(--text);
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
        }

        .footer-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .btn-confirm {
            background: #fff;
            color: #0b1411;
            border: 0;
            padding: 14px 26px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: 0.02em;
        }

        .btn-confirm:hover { background: #e9efec; }

        .pdf-link {
            color: var(--green-soft);
            text-decoration: underline;
            font-size: 14px;
        }

        .disclaimer {
            color: var(--muted);
            font-size: 12.5px;
            margin-top: 18px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div>
            @if ($branding['logo_url'] ?? null)
                <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['company_name'] }}">
            @else
                <strong>{{ $branding['company_name'] }}</strong>
            @endif
        </div>
        <div class="agent">
            <strong>{{ $agent['name'] ?? $branding['company_name'] }}</strong>
            @if (!empty($agent['email']))
                <a href="mailto:{{ $agent['email'] }}">Send Us an Email &rarr;</a>
            @endif
        </div>
    </div>

    <div class="shell">
        <div class="stepper">
            <span class="step active"><span class="dot">1</span> Check &amp; confirm your booking</span>
            <span class="bar"></span>
            <span class="step"><span class="dot">2</span> We confirm details</span>
        </div>

        <h1 class="page-title">Confirm Your Booking</h1>
        <p class="lead">
            Use the form below to confirm your booking with us. After receiving your confirmation we will start securing accommodation, activities and options for your tour. You will receive confirmation from us which will include further information and details of your tour.
        </p>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $currencyCode = $version->currency->short_name ?? 'USD';
            $coverImage = $hero_image;
            $touristModel = $tourist ?? null;
            $touristName = trim((string) (optional($touristModel)->name ?? ''));
            $touristEmail = (string) (optional($touristModel)->email ?? '');
            $guestCount = $version->guest_count ?: 1;
            $startLabel = optional($version->start_date)->format('F j, Y') ?? 'TBD';
            $endLabel = optional($version->end_date)->format('F j, Y') ?? 'TBD';
            $totalAmount = (float) ($version->total_amount ?? 0);
            $perGuest = $guestCount > 0 ? $totalAmount / $guestCount : $totalAmount;
        @endphp

        <div class="section-title">
            <span>Overview Tour</span>
            <span class="ref">Ref. Number: <a href="{{ route('public.itinerary.show', $version->public_token) }}">#{{ $document_reference }}</a></span>
        </div>

        <div class="tour-card">
            <div class="thumb" @if ($coverImage) style="background-image: url('{{ $coverImage }}');" @endif></div>
            <div>
                <div class="duration">{{ $version->duration_days ?: 1 }} Days / {{ $version->duration_nights ?: 0 }} Nights</div>
                <h3>{{ $version->title }}</h3>
                <div class="tour-meta">
                    <div>
                        <span class="k">Start Tour</span>
                        <span class="v">{{ $startLabel }}</span>
                    </div>
                    <div>
                        <span class="k">End Tour</span>
                        <span class="v">{{ $endLabel }}</span>
                    </div>
                    <div>
                        <span class="k">Start Destination</span>
                        <span class="v">{{ $start_destination ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="k">End Destination</span>
                        <span class="v">{{ $end_destination ?: '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="breakdown-title">Breakdown of Costs</div>
        <div class="breakdown">
            <div class="cell label">{{ $guestCount }}x Adult</div>
            <div class="cell qty">{{ $guestCount }}x {{ $currencyCode }} {{ number_format($perGuest, 2) }}</div>
            <div class="cell total">{{ $currencyCode }} {{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="breakdown-total">Total in {{ $currencyCode }}: <strong>{{ $currencyCode }} {{ number_format($totalAmount, 2) }}</strong></div>

        <details class="accordion">
            <summary>What is included?</summary>
            <div class="body">
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
                    <p style="margin: 14px 0 6px; color: var(--text); font-weight: 600;">Excluded</p>
                    <ul>
                        @foreach ($excluded_terms as $term)
                            <li>{{ $term->description }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </details>

        <details class="accordion">
            <summary>What are the payment &amp; booking terms?</summary>
            <div class="body">
                @if ($payment_terms->count() > 0)
                    <ul>
                        @foreach ($payment_terms as $term)
                            <li>
                                @if ($term->title)<strong style="color: var(--text);">{{ $term->title }}:</strong> @endif
                                {{ $term->description }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="margin: 0;">Payment and booking terms will be confirmed by your travel consultant.</p>
                @endif
            </div>
        </details>

        <form action="{{ route('public.itinerary.book', $version->public_token) }}" method="POST">
            @csrf
            <input type="hidden" name="lname" value="{{ $touristModel?->lname ?? '' }}">

            <div class="form-section-title">Personal Information</div>
            <table class="form-table">
                <tr>
                    <td class="label">Name</td>
                    <td class="input">
                        <input name="fname" required value="{{ old('fname', $touristName ?: '') }}" placeholder="Mr./Mrs. Full Name">
                    </td>
                </tr>
                <tr>
                    <td class="label">Email Address</td>
                    <td class="input">
                        <input type="email" name="email" required value="{{ old('email', $touristEmail) }}" placeholder="you@email.com">
                    </td>
                </tr>
            </table>

            <details class="add-message">
                <summary>Add Message <span class="opt">Optional</span></summary>
                <textarea name="note" placeholder="Anything we should know? Dietary needs, arrival flight, special requests…">{{ old('note') }}</textarea>
            </details>

            <div class="footer-bar">
                <button type="submit" class="btn-confirm">Confirm Booking</button>
                @if ($public_pdf_url)
                    <a class="pdf-link" href="{{ $public_pdf_url }}" target="_blank" rel="noopener">View Detailed Quote — PDF</a>
                @endif
            </div>

            <p class="disclaimer">
                Clicking the Confirm Booking button does not take you to payment.<br>
                It only informs us to start the booking process and secure your reservations.
            </p>
        </form>
    </div>
</body>

</html>
