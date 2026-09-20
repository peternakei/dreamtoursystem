@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('quotation_versions.edit', $version->uuid) }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div>
                    <h4 class="page-title mb-1" style="font-size: 1.6em;">Proposal Preview</h4>
                    <p class="text-muted mb-0">{{ $document_reference }} for {{ $tourist->name ?? 'your guest' }}</p>
                </div>
                <div class="d-flex flex-wrap" style="gap: 10px;">
                    <a href="{{ route('quotation_versions.edit', $version->uuid) }}" class="btn btn-outline-primary">Back to Builder</a>
                    <a href="{{ route('quotation_versions.pdf.download', $version->uuid) }}" class="btn btn-outline-dark">Download PDF</a>
                    @if ($version->public_url_enabled)
                        <a href="{{ route('public.itinerary.show', $version->public_token) }}" target="_blank" class="btn btn-outline-success">Open Public Link</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('web.system.quotation.partials.share_draft')

    <div class="row">
        <div class="col-12">
            @include('web.system.quotation.partials.document', ['inlineBookingModal' => true])
        </div>
    </div>

    @include('web.system.quotation.partials.booking_modal')
@endsection

@section('script')
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
                        color: '#AC5526',
                        weight: 4,
                        opacity: 0.9
                    }).addTo(map);
                }

                points.forEach(function(point) {
                    const marker = L.circleMarker([Number(point.latitude), Number(point.longitude)], {
                        radius: 8,
                        color: '#AC5526',
                        fillColor: '#0097DC',
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
@endsection
