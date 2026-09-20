@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-tourists')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-tourist-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Tourist</a>
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
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Country</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tourists as $tourist)
                                            <tr>
                                                <td>{{ $tourist->name }}</td>
                                                <td>{{ $tourist->phone }}</td>
                                                <td>{{ $tourist->country->name }}</td>
                                                <td>{{ $tourist->address }}</td>
                                                <td><span class="badge"
                                                        style="background-color: @if ($tourist->is_active) green @else orangered @endif">
                                                        @if ($tourist->is_active)
                                                            Active
                                                        @else
                                                            Inactive
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $tourist->created_at }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('tourists.show', $tourist->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    @can('edit-tourists')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $tourist->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-tourists')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $tourist->uuid }}');"
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
    @include('web.system.tourist.includes.create_new_tourist_modal')
    @include('web.system.tourist.includes.edit_tourist_details_modal')
    @include('web.system.tourist.includes.delete_tourist_modal')
@endsection
@section('script')
    @include('web.system.tourist.includes.scripts.script')
@endsection

