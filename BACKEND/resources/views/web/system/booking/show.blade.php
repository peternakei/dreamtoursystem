@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('bookings.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Booking Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @can('create-bookings')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#cancel-booking-modal"
                                class="btn btn-danger">Cancel Booking</a>
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
                                        <a href="#trips" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Trip</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#invoices" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Invoice</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#payments" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Payments</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="trips">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if ($booking->trip)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Trip Code</th>
                                                                <th>Name</th>
                                                                <th>Type</th>
                                                                <th>From</th>
                                                                <th>To</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                @if ($booking->trip)
                                                                    <td>{{ $booking->trip->trip_code }}</td>
                                                                    <td>{{ $booking->trip->name }}</td>
                                                                    <td>{{ optional($booking->trip->tripType)->name }}</td>
                                                                    <td>{{ $booking->trip->from_date }}</td>
                                                                    <td>{{ $booking->trip->to_date }}</td>
                                                                    <td class="table-action text-start">
                                                                        <a href="{{ route('trips.show', $booking->trip->uuid) }}"
                                                                            class="action-icon"> <i class="mdi mdi-eye"></i></a>
                                                                    </td>
                                                                @else
                                                                    <td colspan="6" class="text-muted">Booked from a quotation — no trip template attached.</td>
                                                                @endif
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div class="card">
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <h5 class="text-center opacity-50" style="font-size: 1.7em;"><i
                                                                        class="uil uil-search-alt"></i></h5>
                                                                <h5 class="text-center opacity-50"
                                                                    style="font-weight: 500; font-size: 1.3em;">No
                                                                    Trip </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="invoices">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($booking->invoices) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Invoice No</th>
                                                                <th>Invoice Date</th>
                                                                <th>Amount</th>
                                                                <th>Vat</th>
                                                                <th>Total</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($booking->invoices as $invoice)
                                                                <tr>
                                                                    <td>{{ $invoice->invoice_number }}</td>
                                                                    <td>{{ $invoice->invoice_date }}</td>
                                                                    <td>{{ $invoice->currency->short_name }}
                                                                        {{ number_format($invoice->amount) }}</td>
                                                                    <td>{{ $invoice->currency->short_name }}
                                                                        {{ number_format($invoice->vat_amount) }}</td>
                                                                    <td>{{ $invoice->currency->short_name }}
                                                                        {{ number_format($invoice->total_amount) }}</td>
                                                                    <td><span class="badge"
                                                                            style="background-color: {{ $invoice->status->color }}">
                                                                            {{ $invoice->status->name }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="table-action text-start">
                                                                        <a href="{{ route('invoices.show', $invoice->uuid) }}"
                                                                            class="action-icon"> <i class="mdi mdi-eye"></i></a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div class="card">
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <h5 class="text-center opacity-50" style="font-size: 1.7em;"><i
                                                                        class="uil uil-search-alt"></i></h5>
                                                                <h5 class="text-center opacity-50"
                                                                    style="font-weight: 500; font-size: 1.3em;">No
                                                                    Invoice Created!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="payments">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($booking->payments) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Receipt No</th>
                                                                <th>Invoice</th>
                                                                <th>Amount</th>
                                                                <th>Receipt Date</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($booking->payments as $payment)
                                                                <tr>
                                                                    <td>{{ $payment->reference_number }}</td>
                                                                    <td>{{ $payment->invoice->invoice_number }}</td>
                                                                    <td>{{ $payment->currency->short_name }}{{ number_format($payment->amount) }}
                                                                    </td>
                                                                    <td>{{ $payment->receipt_date }}</td>
                                                                    <td class="table-action text-start">
                                                                        <a href="{{ route('receipts.show', $payment->uuid) }}"
                                                                            class="action-icon"> <i class="mdi mdi-eye"></i></a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div class="card">
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <h5 class="text-center opacity-50" style="font-size: 1.7em;"><i
                                                                        class="uil uil-search-alt"></i></h5>
                                                                <h5 class="text-center opacity-50"
                                                                    style="font-weight: 500; font-size: 1.3em;">No
                                                                    Receipt Created!</h5>
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
                    <div class="card" style="border-bottom: 5px {{ $booking->status->color }} solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: {{ $booking->status->color }}"></i>
                                <h5 class="text-center"
                                    style="color: {{ $booking->status->color }}; font-size: 1.2em; font-weight: bold;">
                                    {{ $booking->status->name }}
                                </h5>
                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <p>{{ optional($booking->trip)->description ?? $booking->comments ?? $booking->remarks }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">Booking Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Booking Number</h5>
                                        <h5 style="font-weight: 500;">{!! $booking->booking_number !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Tourist</h5>
                                        <h5 style="font-weight: 500;">{{ $booking->tourist->name }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Phone</h5>
                                        <h5 style="font-weight: 500;">{{ $booking->tourist->phone }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Type</h5>
                                        <h5 style="font-weight: 500;">{{ $booking->bookingType->name }}</h5>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Guests</h5>
                                        <h5 style="font-weight: 500;">{{ $booking->guest_count ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $booking->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $booking->createdBy->userProfile->name ?? '' }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $booking->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            @if (isset($booking->updatedBy))
                                                {{ $booking->updatedBy->userProfile->name }}
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
@endsection
