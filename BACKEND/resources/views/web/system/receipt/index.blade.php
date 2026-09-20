@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-receipts')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-receipt-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Receipt</a>
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
                                            <th>Receipt No</th>
                                            <th>Invoice</th>
                                            <th>Booking</th>
                                            <th>Trip Code</th>
                                            <th>Trip Name</th>
                                            <th>Tourist</th>
                                            <th>Amount</th>
                                            <th>Receipt Date</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($receipts as $receipt)
                                            <tr>
                                                <td>{{ $receipt->receipt_number }}</td>
                                                <td>{{ $receipt->invoice->invoice_number }}</td>
                                                <td>{{ $receipt->invoice->booking->booking_number ?? '' }}</td>
                                                <td>{{ $receipt->invoice->booking->trip->trip_code }}</td>
                                                <td>{{ $receipt->invoice->booking->trip->name }}</td>
                                                <td>{{ $receipt->invoice->tourist->name }}</td>
                                                <td>{{ $receipt->invoice->currency->short_name }} {{ number_format($receipt->amount) }}</td>
                                                <td>{{ $receipt->receipt_date }}</td>
                                                <td><span class="badge"
                                                        style="background-color: {{ $receipt->status->color }}">
                                                        {{ $receipt->status->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $receipt->created_at }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('receipts.show', $receipt->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    {{-- @can('edit-receipts')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $receipt->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-receipts')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $receipt->uuid }}');"
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
