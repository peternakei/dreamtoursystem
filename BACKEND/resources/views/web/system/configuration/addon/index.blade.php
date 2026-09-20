@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-addons')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-addon-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Addon</a>
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
                                            <th>Is Include</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($addons as $addon)
                                            <tr>
                                                <td>{{ $addon->name }}</td>
                                                <td><span class="badge"
                                                        style="background-color: @if($addon->is_include) green @else red  @endif;">
                                                        @if ($addon->is_include)
                                                            Is Include @else Is Not Include
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $addon->created_at }}</td>
                                                <td>{{ $addon->createdBy->userProfile->name }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('addons.show', $addon->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    @can('edit-addons')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $addon->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-addons')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $addon->uuid }}');"
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
    @include('web.system.configuration.addon.includes.create_new_addon_modal')
    @include('web.system.configuration.addon.includes.edit_addon_details_modal')
    @include('web.system.configuration.addon.includes.delete_addon_modal')
@endsection
@section('script')
    @include('web.system.configuration.addon.includes.scripts.script')
@endsection
