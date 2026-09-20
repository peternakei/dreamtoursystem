@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('inquiries.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Inquiry Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @can('change-inquiries-status')
                        @if ($inquiry->is_approved)
                             <a href="{{ route('quotations.create_from_inquiry', $inquiry->uuid) }}"
                                class="btn btn-success">Create Quotation</a>
                        @endif
                           
                        @endcan
                        @can('change-inquiries-status')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#change-inquiry-status-modal"
                                class="btn btn-danger">Change Status</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-8 col-lg-8">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-justified nav-bordered mb-3">
                                    <li class="nav-item">
                                        <a href="#quotations" data-bs-toggle="tab" aria-expanded="true"
                                            class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Quotations</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="quotations">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($inquiry->quotations) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Quotation Number</th>
                                                                <th>Quotation Date</th>
                                                                <th>Amount</th>
                                                                <th>Vat</th>
                                                                <th>Total</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($inquiry->quotations as $quot)
                                                                <tr>
                                                                    <td>{{ $quot->quotation_number }}</td>
                                                                    <td>{{ $quot->quotation_date }}</td>
                                                                    <td>{{ $quot->currency->short_name }}{{ number_format($quot->amount) }}
                                                                    </td>
                                                                    <td>{{ $quot->currency->short_name }}{{ number_format($quot->vat_amount) }}
                                                                    </td>
                                                                    <td>{{ $quot->currency->short_name }}{{ number_format($quot->total_amount) }}
                                                                    </td>
                                                                    <td><span class="badge"
                                                                            style="background-color: {{ $quot->status->color }}">
                                                                            {{ $quot->status->name }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="table-action text-start">
                                                                        <a href="{{ route('quotations.show', $quot->uuid) }}"
                                                                            class="action-icon"> <i
                                                                                class="mdi mdi-eye"></i></a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div class="card">
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <h5 class="text-center opacity-50"
                                                                    style="font-size: 1.7em;"><i
                                                                        class="uil uil-search-alt"></i></h5>
                                                                <h5 class="text-center opacity-50"
                                                                    style="font-weight: 500; font-size: 1.3em;">No
                                                                    Quotation created! </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card"
                        style="border-bottom: 5px @if ($inquiry->is_approved) green @else red @endif solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: @if ($inquiry->is_approved) green @else red @endif"></i>
                                <h5 class="text-center"
                                    style="color: @if ($inquiry->is_approved) green @else red @endif; font-size: 1.2em; font-weight: bold;">
                                    @if ($inquiry->is_approved)
                                        Processed
                                    @else
                                        Pending
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>
                    @if (isset($inquiry->comments))
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <p>{{ $inquiry->comments }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">Inquiry Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Inquiry Number</h5>
                                        <h5 style="font-weight: 500;">{!! $inquiry->inquiry_code !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Tourist</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->tourist->name }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Phone</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->tourist->phone }}</h5>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Name</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->name }}</h5>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Description</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->description }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">From Date</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->from_date }}</h5>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">To Date</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->to_date }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $inquiry->createdBy->userProfile->name ?? '' }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $inquiry->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            @if (isset($inquiry->updatedBy))
                                                {{ $inquiry->updatedBy->userProfile->name }}
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('web.system.inquiry.includes.change_inquiry_status_modal')
@endsection
