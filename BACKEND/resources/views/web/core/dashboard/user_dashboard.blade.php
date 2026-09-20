@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    @php
        $tones = [
            'primary' => 'var(--brand-primary)',
            'secondary' => 'var(--brand-secondary)',
            'accent' => 'var(--brand-primary-dark)',
            'deep' => 'var(--brand-accent-deep)',
        ];
    @endphp

    <section class="main-dashboard">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="page-title mb-1">Dashboard</h4>
                        <p class="text-muted mb-0">A cleaner snapshot of Dream Travel and Tours operations.</p>
                    </div>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">Dream Travel and Tours</li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($dashboard['summary_cards'] as $card)
                <div class="col-sm-6 col-xl-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted text-uppercase small mb-2">{{ $card['label'] }}</p>
                                    <h3 class="mb-0">{{ $card['value'] }}</h3>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; background: {{ $tones[$card['tone']] ?? 'var(--brand-primary)' }}1A; color: {{ $tones[$card['tone']] ?? 'var(--brand-primary)' }};">
                                    <i class="{{ $card['icon'] }}" style="font-size: 1.35rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mt-1">
            @foreach ($dashboard['highlights'] as $highlight)
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3">
                            <p class="text-muted small mb-1">{{ $highlight['label'] }}</p>
                            <h5 class="mb-0">{{ $highlight['value'] }}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div id="quotation_status_summary" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div id="trip_category_summary" style="min-height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div id="monthly_performance_chart" style="min-height: 380px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h5 class="mb-1">Destinations Map</h5>
                                <p class="text-muted mb-0">Showing destinations with saved coordinates.</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">{{ count($dashboard['destinations_map']) }} mapped</span>
                        </div>
                        <div id="map" style="height: 360px; width: 100%; border-radius: 12px; overflow: hidden;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const dashboard = @json($dashboard);
        const chartColors = ['#1CA9DC', '#288479', '#B9572B', '#133A5E', '#5CB85C', '#F0AD4E'];

        document.addEventListener('DOMContentLoaded', function() {
            renderPieChart('quotation_status_summary', 'Quotations by Status', dashboard.quotation_status_summary,
                'Quotations');
            renderPieChart('trip_category_summary', 'Trips by Category', dashboard.trip_category_summary, 'Trips');
            renderMonthlyPerformanceChart();
            initMap();
        });

        function renderPieChart(targetId, title, data, seriesName) {
            Highcharts.chart(targetId, {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent'
                },
                title: {
                    text: title
                },
                subtitle: {
                    text: 'Source: Dream Travel and Tours'
                },
                colors: chartColors,
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.y}'
                        }
                    }
                },
                series: [{
                    name: seriesName,
                    colorByPoint: true,
                    data: data
                }]
            });
        }

        function renderMonthlyPerformanceChart() {
            Highcharts.chart('monthly_performance_chart', {
                chart: {
                    zooming: {
                        type: 'xy'
                    },
                    backgroundColor: 'transparent'
                },
                title: {
                    text: `Monthly Revenue and Bookings (${dashboard.year})`
                },
                subtitle: {
                    text: 'Source: Dream Travel and Tours'
                },
                credits: {
                    enabled: false
                },
                colors: [chartColors[0], chartColors[2]],
                xAxis: [{
                    categories: dashboard.monthly_performance.categories,
                    crosshair: true
                }],
                yAxis: [{
                    title: {
                        text: 'Revenue (TZS)'
                    }
                }, {
                    title: {
                        text: 'Bookings'
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true
                },
                series: [{
                    name: 'Revenue',
                    type: 'column',
                    data: dashboard.monthly_performance.revenue,
                    tooltip: {
                        valueDecimals: 0
                    }
                }, {
                    name: 'Bookings',
                    type: 'spline',
                    yAxis: 1,
                    data: dashboard.monthly_performance.bookings
                }]
            });
        }

        function initMap() {
            const destinations = dashboard.destinations_map || [];
            const mapElement = document.getElementById('map');
            if (!mapElement || typeof L === 'undefined') {
                if (mapElement) {
                    mapElement.innerHTML =
                        '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Map could not be loaded.</div>';
                }
                return;
            }

            const fallbackCenter = [-3.3869, 36.68299];
            const center = destinations.length ? [
                Number(destinations[0].latitude),
                Number(destinations[0].longitude)
            ] : fallbackCenter;

            const map = L.map(mapElement, {
                scrollWheelZoom: false
            }).setView(center, destinations.length ? 6 : 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const bounds = [];

            const markerIcon = L.divIcon({
                className: 'seren-map-marker',
                html: '<div style="width:14px;height:14px;border-radius:999px;background:#1CA9DC;border:3px solid #288479;box-shadow:0 0 0 4px rgba(28,169,220,.18);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            destinations.forEach(function(destination) {
                const point = [Number(destination.latitude), Number(destination.longitude)];
                bounds.push(point);

                L.marker(point, {
                        icon: markerIcon,
                        title: destination.name
                    }).addTo(map)
                    .bindPopup(`
                        <div style="max-width: 220px;">
                            <h6 style="margin-bottom: 6px;">${destination.name}</h6>
                            <p style="margin-bottom: 4px;">${destination.description || 'No description available.'}</p>
                            <small>${destination.location || ''}${destination.region ? `, ${destination.region}` : ''}</small>
                        </div>
                    `);
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            }
        }
    </script>
@endsection
