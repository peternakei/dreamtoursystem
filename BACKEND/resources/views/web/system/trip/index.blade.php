@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-trips')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-trip-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Trip</a>
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
                                            <th>Trip Code</th>
                                            <th>Name</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Type</th>
                                            <th>Source</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($trips as $trip)
                                            <tr>
                                                <td>{{ $trip->trip_code }}</td>
                                                <td>{{ Str::limit($trip->name,80,'...') }}</td>
                                                <td>{{ $trip->from_date }}</td>
                                                <td>{{ $trip->to_date }}</td>
                                                <td><span class="badge"
                                                        style="background-color: {{ $trip->tripType?->color ?? '#94a3b8' }}">
                                                        {{ $trip->tripType?->name ?? '—' }}
                                                    </span>
                                                </td>
                                                <td><span class="badge"
                                                        style="background-color: {{ $trip->tripSource?->color ?? '#94a3b8' }}">
                                                        {{ $trip->tripSource?->name ?? '—' }}
                                                    </span>
                                                </td>
                                                <td><span class="badge"
                                                        style="background-color: {{ $trip->tripStatus?->color ?? '#94a3b8' }}">
                                                        {{ $trip->tripStatus?->name ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>{{ $trip->created_at }}</td>
                                                <td>{{ $trip->createdBy?->userProfile?->name ?? '—' }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('trips.show', $trip->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
                                                    @can('edit-trips')
                                                        <a href="javascript:void(0);"
                                                            onclick="getEditDetails('{{ $trip->uuid }}');"
                                                            class="action-icon text-primary"><i class="uil uil-edit"></i>
                                                            Edit</a>
                                                    @endcan
                                                    @can('delete-trips')
                                                        <a href="javascript:void(0);"
                                                            onclick="getDeleteDetails('{{ $trip->uuid }}');"
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
    @include('web.system.trip.includes.create_new_trip_modal')
    @include('web.system.trip.includes.edit_trip_details_modal')
    @include('web.system.trip.includes.delete_trip_modal')
@endsection
@section('script')
    @include('web.system.trip.includes.scripts.script')
@endsection
