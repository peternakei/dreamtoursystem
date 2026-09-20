@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-bank-details')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-bank-account-details-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Account Details</a>
                        </a>
                    @endcan
                </div>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">RENTAL </a></li>
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
                                            <th>Bank</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Currency</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bankDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->bank->name }}</td>
                                                <td>{{ $detail->account_name }}</td>
                                                <td>{{ $detail->account_number }}</td>
                                                <td>{{ $detail->currency->short_name }}</td>
                                                <td><span class="badge"
                                                        style="background-color: @if ($detail->is_active) green @else orangered @endif">
                                                        @if ($detail->is_active)
                                                            Active
                                                        @else
                                                            Inactive
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $detail->created_at }}</td>
                                                <td>{{ $detail->createdBy->userProfile->name }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('bank_details.show', $detail->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    @can('edit-bank-details')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $detail->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-bank-details')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $detail->uuid }}');"
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
    @include('web.system.configuration.bank_details.includes.create_new_bank_details_modal')
    @include('web.system.configuration.bank_details.includes.edit_bank_details_modal')
    @include('web.system.configuration.bank_details.includes.delete_bank_details_modal')
@endsection
@section('script')
    @include('web.system.configuration.bank_details.includes.scripts.script')
@endsection
