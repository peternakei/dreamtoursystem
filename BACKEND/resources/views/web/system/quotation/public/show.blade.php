<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $document_reference }} - Dream Travel and Tours</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            background: #f4f1ea;
            font-family: Arial, Helvetica, sans-serif;
            color: #17342d;
        }

        .public-shell {
            max-width: 1220px;
            margin: 0 auto;
            padding: 32px 18px 56px;
        }

    </style>
</head>

<body>
    <div class="public-shell">
        @if (session('success'))
            <div style="background: #dff6e9; border: 1px solid #93d6ad; padding: 14px 16px; border-radius: 14px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @include('web.system.quotation.partials.document', ['publicMode' => true, 'inlineBookingModal' => true])
    </div>

    @include('web.system.quotation.partials.booking_modal', [
        'bookingEndpoint' => route('public.itinerary.book', $version->public_token),
    ])

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof L === 'undefined') {
                return;
            }

            document.querySelectorAll('.js-quote-route-map').forEach(function(mapElement, index) {
                if (mapElement.dataset.mapReady === '1') {
                    return;
                }

                let points = [];

                try {
                    points = JSON.parse(mapElement.dataset.routePoints || '[]');
                } catch (error) {
                    points = [];
                }

                if (points.length < 1) {
                    return;
                }

                const map = L.map(mapElement, {
                    scrollWheelZoom: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                const latLngs = points.map(function(point) {
                    return [Number(point.latitude), Number(point.longitude)];
                });

                if (latLngs.length > 1) {
                    L.polyline(latLngs, {
                        color: '#0097DC',
                        weight: 4,
                        opacity: 0.9
                    }).addTo(map);
                }

                points.forEach(function(point) {
                    const marker = L.circleMarker([Number(point.latitude), Number(point.longitude)], {
                        radius: 8,
                        color: '#0097DC',
                        fillColor: '#AC5526',
                        fillOpacity: 1,
                        weight: 3
                    }).addTo(map);

                    marker.bindPopup('<strong>' + point.order + '. ' + point.name + '</strong>');
                    marker.bindTooltip(point.order + '. ' + point.name, {
                        permanent: true,
                        direction: 'top',
                        offset: [0, -10],
                        className: 'quote-route-label'
                    });
                });

                if (latLngs.length > 1) {
                    map.fitBounds(latLngs, { padding: [20, 20] });
                } else {
                    map.setView(latLngs[0], 9);
                }

                mapElement.dataset.mapReady = '1';
                setTimeout(function() {
                    map.invalidateSize();
                }, 150 + (index * 50));
            });
        });
    </script>
</body>

</html>
