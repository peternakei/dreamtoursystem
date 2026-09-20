@extends('layouts.app')

@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm">All</a>
                    <a href="{{ route('quotations.open') }}" class="btn btn-outline-primary btn-sm">Open</a>
                    <a href="{{ route('quotations.won') }}" class="btn btn-outline-success btn-sm">Won</a>
                    <a href="{{ route('quotations.lost') }}" class="btn btn-outline-danger btn-sm">Lost</a>
                </div>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">SBS</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">{{ $sub_title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row mt-1">
                            <div class="col-12">
                                <table id="fixed-header-datatable"
                                    class="table table-sm dt-responsive nowrap table-hover w-100">
                                    <thead class="pt-2" style="background-color: #e9ecef;">
                                        <tr>
                                            <th>Quotation Number</th>
                                            <th>Tourist Name</th>
                                            <th>Request</th>
                                            <th>Trip</th>
                                            <th>Version</th>
                                            <th>Travel Dates</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Updated At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotations as $quotation)
                                        @php
                                            $currentVersion = $quotation->currentVersion;
                                            $currencyCode = $currentVersion?->currency?->short_name ?? $quotation->currency?->short_name ?? 'USD';
                                        @endphp
                                        <tr>
                                            <td>{{ $quotation->quotation_number ?? 'N/A' }}</td>
                                            <td>{{ $quotation->tourist->name ?? 'N/A' }}</td>
                                            <td>{{ $quotation->inquiry->request_reference ?? $quotation->inquiry->inquiry_code ?? 'N/A' }}</td>
                                            <td>{{ $quotation->createdFromTrip->name ?? $currentVersion?->trip?->name ?? 'Tailor-made quote' }}</td>
                                            <td>{{ $currentVersion?->reference_number ?? 'No version' }}</td>
                                            <td>
                                                {{ optional($currentVersion?->start_date)->format('M d, Y') ?? 'TBD' }}
                                                -
                                                {{ optional($currentVersion?->end_date)->format('M d, Y') ?? 'TBD' }}
                                            </td>
                                            <td>{{ $currencyCode }} {{ number_format($quotation->total_amount ?? 0, 2) }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $quotation->status->color ?? '#6c757d' }};">
                                                    {{ $quotation->status->name ?? 'Open' }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($quotation->updated_at)->format('M d, Y H:i') }}</td>
                                            <td class="table-action">
                                                <a href="{{ route('quotations.show', $quotation->uuid) }}" class="action-icon text-info">
                                                    <i class="uil uil-eye"></i> Show
                                                </a>
                                                @if ($currentVersion)
                                                    <a href="{{ route('quotation_versions.edit', $currentVersion->uuid) }}" class="action-icon text-primary">
                                                        <i class="uil uil-edit"></i> Builder
                                                    </a>
                                                    <a href="{{ route('quotation_versions.preview', $currentVersion->uuid) }}" class="action-icon text-success">
                                                        <i class="uil uil-window"></i> Preview
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
