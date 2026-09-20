<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $document_reference }} - {{ $branding['company_name'] }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        @page {
            size: A4;
            margin: 14mm 8mm 10mm 8mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #f4f1ea;
            font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
            color: #0097DC;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            padding: 0;
        }

        .pdf-shell {
            max-width: 1120px;
            margin: 0 auto;
        }

        .no-print,
        .public-toolbar,
        .public-action-bar,
        .public-modal {
            display: none !important;
        }

        .quote-document .quote-section,
        .quote-document .quote-brand-strip,
        .quote-document .quote-footer-bar,
        .quote-document .quote-day-card,
        .quote-document .quote-gallery-card,
        .quote-document .quote-price-highlight,
        .quote-document .quote-map-wrap,
        .quote-document .quote-summary-box,
        .quote-document .quote-cover,
        .quote-document .quote-company {
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        .quote-document .quote-title,
        .quote-document .quote-subtitle {
            break-after: avoid;
            page-break-after: avoid;
        }

        .quote-document .quote-cover,
        .quote-document .quote-brand-strip,
        .quote-document .quote-price-highlight,
        .quote-document .quote-company,
        .quote-document .quote-gallery-image,
        .quote-document .quote-day-image {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .quote-document .quote-section {
            margin-bottom: 12px;
        }

        .quote-document .quote-body {
            padding: 20px 22px;
        }

        .quote-document .quote-brand-strip {
            margin-bottom: 8px;
            padding: 12px 20px;
        }

        .quote-document button.quote-cta-button {
            pointer-events: none;
        }

        @media print {

            html,
            body {
                background: #f4f1ea !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="pdf-shell">
        @php
            // Match the digital itinerary (/itinerary/<token>) exactly: screen-mode
            // partial, Leaflet route map, public-styled cover. Browsershot renders
            // the same HTML the guest sees in the browser.
            $publicMode = false;
            $document_mode = 'screen';
            $force_static_map = false;
            $includeDocumentStyles = true;
        @endphp

        @include('web.system.quotation.partials.document', [
            'publicMode' => false,
            'document_mode' => 'screen',
            'force_static_map' => false,
            'includeDocumentStyles' => true,
        ])
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
                    scrollWheelZoom: false,
                    zoomControl: false,
                    attributionControl: false,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                }).addTo(map);

                const latLngs = points.map(function(point) {
                    return [Number(point.latitude), Number(point.longitude)];
                });

                if (latLngs.length > 1) {
                    L.polyline(latLngs, {
                        color: '#0097DC',
                        weight: 4,
                        opacity: 0.9,
                    }).addTo(map);
                }

                points.forEach(function(point) {
                    const marker = L.circleMarker([Number(point.latitude), Number(point.longitude)], {
                        radius: 8,
                        color: '#0097DC',
                        fillColor: '#AC5526',
                        fillOpacity: 1,
                        weight: 3,
                    }).addTo(map);

                    marker.bindTooltip(point.order + '. ' + point.name, {
                        permanent: true,
                        direction: 'top',
                        offset: [0, -10],
                        className: 'quote-route-label',
                    });
                });

                if (latLngs.length > 1) {
                    map.fitBounds(latLngs, {
                        padding: [24, 24]
                    });
                } else {
                    map.setView(latLngs[0], 9);
                }

                mapElement.dataset.mapReady = '1';
                map.invalidateSize();
            });
        });
    </script>
</body>

</html>
