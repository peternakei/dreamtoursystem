@extends('layouts.app')

@section('content')
    @php
        $currentVersion = $quotation->currentVersion;
        $currencyCode = $currentVersion?->currency?->short_name ?? $quotation->currency?->short_name ?? 'USD';
    @endphp

    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('quotations.index') }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="page-title mb-1" style="font-size: 1.6em;">{{ $quotation->quotation_number }}</h4>
                    <p class="text-muted mb-0">Quotation record for {{ $quotation->tourist->name ?? 'guest' }} linked to request {{ $quotation->inquiry->request_reference ?? $quotation->inquiry->inquiry_code ?? 'N/A' }}.</p>
                </div>
                <div class="d-flex flex-wrap" style="gap: 10px;">
                    @if ($currentVersion)
                        <a href="{{ route('quotation_versions.edit', $currentVersion->uuid) }}" class="btn btn-primary">Open Builder</a>
                        <a href="{{ route('quotation_versions.preview', $currentVersion->uuid) }}" class="btn btn-outline-success">Preview</a>
                        <form action="{{ route('quotations.duplicate_version', $quotation->uuid) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">Duplicate Version</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Commercial Summary</h5>
                        <span class="badge" style="background-color: {{ $quotation->status->color ?? '#6c757d' }};">
                            {{ $quotation->status->name ?? 'Open' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Guest</div>
                        <div class="fw-semibold">{{ $quotation->tourist->name ?? 'N/A' }}</div>
                        <div class="text-muted">{{ $quotation->tourist->email ?? 'No email' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Source Trip</div>
                        <div class="fw-semibold">{{ $quotation->createdFromTrip->name ?? 'Tailor-made' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Current Version</div>
                        <div class="fw-semibold">{{ $currentVersion?->reference_number ?? 'No version' }}</div>
                        <div class="text-muted text-uppercase">{{ $currentVersion?->status ?? 'draft' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Travel Dates</div>
                        <div class="fw-semibold">
                            {{ optional($currentVersion?->start_date)->format('M d, Y') ?? 'TBD' }}
                            to
                            {{ optional($currentVersion?->end_date)->format('M d, Y') ?? 'TBD' }}
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted small">Current Total</div>
                        <div class="fw-semibold" style="font-size: 1.35em;">{{ $currencyCode }} {{ number_format($quotation->total_amount ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            @if ($currentVersion)
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3">Quote Health</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Itinerary days</span>
                            <span class="fw-semibold">{{ $currentVersion->days->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Visible price lines</span>
                            <span class="fw-semibold">{{ $currentVersion->priceLines->where('is_visible', true)->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Included terms</span>
                            <span class="fw-semibold">{{ $currentVersion->terms->where('type', 'included')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Payment terms</span>
                            <span class="fw-semibold">{{ $currentVersion->paymentTerms->count() }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                        <div>
                            <h5 class="mb-1">Version Timeline</h5>
                            <p class="text-muted mb-0">Each version preserves its own itinerary, price lines, and PDF snapshot.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="fixed-header-datatable" class="table table-sm dt-responsive nowrap table-hover w-100">
                            <thead style="background-color: #e9ecef;">
                                <tr>
                                    <th>Reference</th>
                                    <th>Title</th>
                                    <th>Travel Window</th>
                                    <th>Guests</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Public</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($quotation->versions as $version)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $version->reference_number }}</div>
                                            <small class="text-muted">Version {{ $version->version_number }}</small>
                                        </td>
                                        <td>{{ $version->title }}</td>
                                        <td>
                                            {{ optional($version->start_date)->format('M d, Y') ?? 'TBD' }}
                                            <br>
                                            <small class="text-muted">{{ optional($version->end_date)->format('M d, Y') ?? 'TBD' }}</small>
                                        </td>
                                        <td>{{ $version->guest_count }}</td>
                                        <td>{{ $version->currency->short_name ?? $currencyCode }} {{ number_format($version->total_amount ?? 0, 2) }}</td>
                                        <td><span class="badge bg-light text-dark text-uppercase">{{ $version->status }}</span></td>
                                        <td>
                                            @if ($version->public_url_enabled)
                                                <span class="badge bg-success-subtle text-success-emphasis">Enabled</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Hidden</span>
                                            @endif
                                        </td>
                                        <td class="table-action">
                                            <a href="{{ route('quotation_versions.edit', $version->uuid) }}" class="action-icon text-primary">
                                                <i class="uil uil-edit"></i> Builder
                                            </a>
                                            <a href="{{ route('quotation_versions.preview', $version->uuid) }}" class="action-icon text-success">
                                                <i class="uil uil-window"></i> Preview
                                            </a>
                                            <a href="{{ route('quotation_versions.pdf.download', $version->uuid) }}" class="action-icon text-danger">
                                                <i class="uil uil-file-download"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No versions have been created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
