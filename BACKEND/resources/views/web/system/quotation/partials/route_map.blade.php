@php
    $points = collect($route_points ?? [])->values();
    $svgWidth = 460;
    $svgHeight = 220;
    $padding = 26;
    $plotPoints = collect();

    if ($points->count() > 0) {
        $minLat = $points->min('latitude');
        $maxLat = $points->max('latitude');
        $minLng = $points->min('longitude');
        $maxLng = $points->max('longitude');

        $latRange = max(($maxLat - $minLat), 1);
        $lngRange = max(($maxLng - $minLng), 1);

        $plotPoints = $points->map(function ($point) use ($minLat, $latRange, $minLng, $lngRange, $svgWidth, $svgHeight, $padding) {
            $x = $padding + ((($point['longitude'] - $minLng) / $lngRange) * ($svgWidth - ($padding * 2)));
            $y = $svgHeight - $padding - ((($point['latitude'] - $minLat) / $latRange) * ($svgHeight - ($padding * 2)));

            return [
                'name' => $point['name'],
                'order' => $point['order'],
                'x' => round($x, 2),
                'y' => round($y, 2),
            ];
        });
    }
@endphp

@php
    // `force_static_map` is the sole switch — the PDF template renders a
    // real Leaflet map (same as the digital itinerary) so the two paths
    // stay visually identical. Callers that can't run JS (DomPDF fallback)
    // pass force_static_map=true to fall back to the SVG sketch.
    $useStaticMap = (bool) ($force_static_map ?? false);
@endphp
@if ($useStaticMap)
    @if ($plotPoints->count() > 1)
        <svg viewBox="0 0 {{ $svgWidth }} {{ $svgHeight }}" width="100%" height="220" xmlns="http://www.w3.org/2000/svg">
            <rect x="0" y="0" width="{{ $svgWidth }}" height="{{ $svgHeight }}" rx="18" fill="#f7f3ec" />
            <defs>
                <linearGradient id="routeLineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#AC5526" />
                    <stop offset="100%" stop-color="#0097DC" />
                </linearGradient>
            </defs>
            <polyline
                points="{{ $plotPoints->map(fn($point) => $point['x'] . ',' . $point['y'])->implode(' ') }}"
                fill="none"
                stroke="url(#routeLineGradient)"
                stroke-width="5"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-dasharray="10 6"
            />

            @foreach ($plotPoints as $point)
                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="8" fill="#ffffff" stroke="#AC5526" stroke-width="4" />
                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" fill="#0097DC" />
                <text x="{{ $point['x'] + 10 }}" y="{{ max($point['y'] - 12, 18) }}" font-size="12" fill="#AC5526" font-weight="700">
                    {{ \Illuminate\Support\Str::limit($point['name'], 18) }}
                </text>
                <text x="{{ $point['x'] - 4 }}" y="{{ $point['y'] + 24 }}" font-size="11" fill="#6c757d" font-weight="700">
                    {{ $point['order'] }}
                </text>
            @endforeach
        </svg>
    @else
        <div style="padding: 12px 0 4px;">
            @forelse ($route_destinations as $index => $destination)
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="width: 28px; height: 28px; border-radius: 999px; background: #AC5526; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-right: 10px;">
                        {{ $index + 1 }}
                    </div>
                    <div style="font-weight: 600;">{{ $destination->name }}</div>
                </div>
            @empty
                <div class="text-muted">A route sketch will appear here once destination coordinates are attached to the quote.</div>
            @endforelse
        </div>
    @endif
@else
    @if ($points->count() >= 1)
        <div
            class="quote-leaflet-map js-quote-route-map"
            data-route-points='@json($points)'
            data-map-label="{{ $document_reference }}"
        ></div>
        <div class="quote-route-caption">Interactive route map powered by Leaflet and the destination coordinates already stored in your library.</div>
    @else
        <div class="text-muted" style="padding: 12px 0 4px;">A route sketch will appear here once destination coordinates are attached to the quote.</div>
    @endif
@endif
