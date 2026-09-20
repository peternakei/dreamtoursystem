@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <!-- <div class="dbtn">
                    @can('create-inquiries')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-inquiry-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Inquiry</a>
                        </a>
                    @endcan
                </div> -->
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
                                            <th>Inquiry Number</th>
                                            <th>Tourist Name</th>
                                            <th>Trip Type</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Guests</th>
                                            <th>Budget</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inquiries as $inquiry)
                                            <tr>
                                                <td>{{ $inquiry->inquiry_code ?? 'N/A' }}</td>
                                                <td>{{ $inquiry->tourist->name ?? 'N/A' }}</td>
                                                <td>{{ $inquiry->tripType->name ?? 'N/A' }}</td>
                                                <td>{{ $inquiry->from_date ? \Carbon\Carbon::parse($inquiry->from_date)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td>{{ $inquiry->to_date ? \Carbon\Carbon::parse($inquiry->to_date)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td>{{ $inquiry->guests ?? 'N/A' }}</td>
                                                <td>{{ $inquiry->budget ?? 'N/A' }}</td>
                                                <td><span class="badge"
                                                        style="background-color: @if ($inquiry->status === 'approved') green @elseif ($inquiry->status === 'pending') orangered @else blue @endif">
                                                        @if ($inquiry->status === 'approved')
                                                            Approved
                                                        @elseif ($inquiry->status === 'pending')
                                                            Pending
                                                        @else
                                                            {{ ucfirst($inquiry->status) }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $inquiry->created_at ? \Carbon\Carbon::parse($inquiry->created_at)->format('M d, Y H:i') : 'N/A' }}
                                                </td>
                                                <td class="table-action">
                                                    <a href="{{ route('inquiries.show', $inquiry->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    @can('edit-inquiries')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $inquiry->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-inquiries')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $inquiry->uuid }}');"
                                                            class="action-icon text-danger"><i class="uil uil-trash"></i>
                                                            Delete</a>
                                                    @endcan
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
    @include('web.system.inquiry.includes.create_new_inquiry_modal')
    @include('web.system.inquiry.includes.edit_inquiry_details_modal')
    @include('web.system.inquiry.includes.delete_inquiry_modal')
@endsection
@section('script')
    @include('web.system.inquiry.includes.scripts.script')
@endsection
