@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('users.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">User Profile</h4>

                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @if ($user->is_active)
                            @can('assign-user-roles')
                                <a href="#" data-bs-toggle="modal" data-bs-target="#assign-user-role-modal"
                                    class="btn btn-info">Assign Role</a>
                            @endcan
                        @endif
                        <a class="btn btn-sm btn-soft-primary dropdown-toggle arrow-none text-center"
                            data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                            aria-expanded="false"><span style="font-size: 1.2em;"><i class="uil-ellipsis-h"></i></span>
                            More Actions</a>
                        <div class="dropdown-menu dropdown-menu-animated shadow-lg profile-dropdown">
                            <!-- item-->
                            @can('reset-user-passwords')
                                <!-- item-->
                                <a href="#" data-bs-toggle="modal" data-bs-target="#reset-user-modal"
                                    class="dropdown-item">
                                    <i class="mdi mdi-refresh me-1"></i>
                                    <span>Reset Password</span>
                                </a>
                            @endcan
                            @can('change-user-statuses')
                                <!-- item-->
                                <a href="#" data-bs-toggle="modal" data-bs-target="#change-user-status-modal"
                                    class="dropdown-item">
                                    <i class="mdi mdi-poker-chip me-1"></i>
                                    <span>Change User Status</span>
                                </a>
                            @endcan

                        </div>
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
                                        <a href="#roles" data-bs-toggle="tab" aria-expanded="true"
                                            class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Roles</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#permissions" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Permissions</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#activities" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Activities</span>
                                        </a>
                                    </li>

                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="roles">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($user->roles) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($user->roles as $role)
                                                                <tr>
                                                                    <td>{!! $role->role->name !!}</td>
                                                                    <td class="table-action">
                                                                        <a href="#" data-bs-toggle="tooltip"
                                                                            data-bs-placement="right"
                                                                            data-bs-title="Revoke Role"
                                                                            onclick="revokeUserRole('{{ $role->role->name }}','{{ $role->role->id }}')"
                                                                            class="action-icon">
                                                                            <i class="uil uil-trash"></i></a>
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
                                                                    User Roles added!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="permissions">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($user->permissions) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($user->permissions as $permission)
                                                                <tr>
                                                                    <td>{!! $permission->permission->name !!}</td>
                                                                    <td class="table-action">
                                                                        <a href="#" class="action-icon">
                                                                            <i class="uil uil-brush-alt"></i></a>
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
                                                                    User Permission added!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="activities">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (1 < 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

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
                                                                    User Activity added!</h5>
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
                        style="border-bottom: 5px @if ($user->is_active) green @else crimson @endif solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: @if ($user->is_active) green @else crimson @endif"></i>
                                <h5 class="text-center"
                                    style="color: @if ($user->is_active) green @else crimson @endif; font-size: 1.2em; font-weight: bold;">
                                    @if ($user->is_active)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </h5>
                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">User Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Name</h5>
                                        <h5 style="font-weight: 500;">{!! $user->name !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Phone</h5>
                                        <h5 style="font-weight: 500;">{!! $user->phone !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Email</h5>
                                        <h5 style="font-weight: 500;">{!! $user->email !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $user->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">{{ $user->createdBy->userProfile->name }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $user->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $user->createdBy->userProfile->name ?? '' }}
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
    @include('web.core.user.includes.reset_user_password_modal')
    @include('web.core.user.includes.change_user_status_modal')
    @include('web.core.user.includes.assign_user_role_modal')
    @include('web.core.user.includes.revoke_user_role_modal')
@endsection
@section('script')
    @include('web.core.user.includes.scripts.script')
@endsection
