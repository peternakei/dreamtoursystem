<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Booking received — {{ $document_reference }}</title>
    <style>
        :root {
            --bg: #0b1411;
            --line: #1f3a32;
            --muted: #9bb0aa;
            --text: #e8efec;
            --green-soft: #74b87a;
            --green: #4caf50;
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
            max-width: 720px;
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

        .thank-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 6px 0 14px;
        }

        .check {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--green-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--green-soft);
            font-size: 18px;
            font-weight: 700;
        }

        h1.page-title {
            font-size: 26px;
            font-weight: 800;
            margin: 0;
        }

        .lead {
            color: var(--text);
            font-size: 14.5px;
            margin: 0 0 22px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin: 18px 0 26px;
        }

        .btn {
            display: inline-block;
            padding: 14px 22px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            font-family: inherit;
            border: 0;
            cursor: pointer;
            letter-spacing: 0.02em;
        }

        .btn.primary {
            background: var(--green);
            color: #fff;
        }

        .btn.primary:hover { background: #43a047; }

        .btn.secondary {
            background: #fff;
            color: #0b1411;
        }

        .btn.secondary:hover { background: #e9efec; }

        .agent-card {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            padding: 16px 0;
            font-size: 14px;
        }

        .agent-card strong {
            display: block;
            margin-bottom: 2px;
        }

        .agent-card .company {
            color: var(--muted);
        }

        .reference {
            color: var(--muted);
            font-size: 12.5px;
            margin-top: 18px;
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
            <span class="step active"><span class="dot">&#10003;</span> Check &amp; confirm your booking</span>
            <span class="bar"></span>
            <span class="step active"><span class="dot">2</span> We confirm details</span>
        </div>

        <div class="thank-row">
            <span class="check">&#10003;</span>
            <h1 class="page-title">Thank you very much {{ $tourist?->name ?? 'Guest' }}!</h1>
        </div>

        <p class="lead">
            We have received your booking confirmation. You will soon receive further instructions to get your tour started. Feel free to contact us if you have any questions.<br>
            We look forward to meeting you!
        </p>

        <div class="actions">
            <a href="{{ route('public.itinerary.show', $version->public_token) }}" class="btn primary">View Your Digital Itinerary</a>
            @if ($public_pdf_url)
                <a href="{{ $public_pdf_url }}" class="btn secondary" target="_blank" rel="noopener">Download Quote</a>
            @endif
        </div>

        <div class="agent-card">
            <strong>{{ $agent['name'] ?? $branding['company_name'] }}</strong>
            <span class="company">{{ $branding['company_name'] }}</span>
        </div>

        @if (session('booking_reference'))
            <div class="reference">Booking reference: <strong style="color: var(--text);">{{ session('booking_reference') }}</strong></div>
        @endif
    </div>
</body>

</html>
