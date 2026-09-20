@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-seasons')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-season-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Season</a>
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
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($seasons as $season)
                                            <tr>
                                                <td>{{ $season->name }}</td>
                                                <td>{{ $season->dates()->where('is_active', true)->value('start_date') }}</td>
                                                <td>{{ $season->dates()->where('is_active', true)->value('end_date') }}</td>
                                                <td><span class="badge"
                                                        style="background-color: green;">
                                                        Active
                                                    </span>
                                                </td>
                                                <td>{{ $season->created_at }}</td>
                                                <td>{{ $season->createdBy?->userProfile?->name ?? '—' }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('seasons.show', $season->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
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
    @include('web.system.configuration.season.includes.create_new_season_modal')
@endsection
@section('script')
    @include('web.system.configuration.season.includes.scripts.script')
@endsection
