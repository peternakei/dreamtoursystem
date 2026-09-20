@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-menus')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-menu-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Menu</a>
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
                                            <th>Title</th>
                                            <th>Name</th>
                                            <th>Url</th>
                                            <th>Icon</th>
                                            <th>Ordering</th>
                                            <th>Parent</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($menus as $menu)
                                            <tr>
                                                <td>{{ $menu->title }}</td>
                                                <td>{{ $menu->name }}</td>
                                                <td>{{ $menu->url ?? '' }}</td>
                                                <td>{{ $menu->icon ?? '' }}</td>
                                                <td>{{ $menu->ordering }}</td>
                                                <td><span class="">{{ $menu->parentMenu->name ?? '' }}</span></td>
                                                <td><span class="badge"
                                                        style="background-color: @if ($menu->is_active) green @else orangered @endif">
                                                        @if ($menu->is_active)
                                                            Active
                                                        @else
                                                            Inactive
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $menu->created_at }}</td>
                                                <td class="table-action">
                                                    @can('edit-menus')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $menu->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-menus')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $menu->uuid }}');"
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
    @include('web.core.menu.includes.create_new_menu_modal')
    @include('web.core.menu.includes.edit_menu_details_modal')
    @include('web.core.menu.includes.delete_menu_modal')
@endsection
@section('script')
    @include('web.core.menu.includes.scripts.script')
@endsection
