@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-invoices')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-invoice-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Invoice</a>
                        </a>
                    @endcan
                </div>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">SBS </a></li>
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
                                            <th>Invoice No</th>
                                            <th>Booking</th>
                                            <th>Trip Code</th>
                                            <th>Trip Name</th>
                                            <th>Tourist</th>
                                            <th>Amount</th>
                                            <th>Invoice Date</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>{{ $invoice->booking->booking_number ?? '' }}</td>
                                                <td>{{ $invoice->booking->trip->trip_code }}</td>
                                                <td>{{ $invoice->booking->trip->name }}</td>
                                                <td>{{ $invoice->tourist->name }}</td>
                                                <td>{{ $invoice->currency->short_name }} {{ number_format($invoice->total_amount) }}</td>
                                                <td>{{ $invoice->invoice_date }}</td>
                                                <td><span class="badge"
                                                        style="background-color: {{ $invoice->status->color }}">
                                                        {{ $invoice->status->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $invoice->created_at }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('invoices.show', $invoice->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    {{-- @can('edit-invoices')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $invoice->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-invoices')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $invoice->uuid }}');"
                                                            class="action-icon text-danger"><i class="uil uil-trash"></i>
                                                            Delete</a>
                                                    @endcan --}}
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
