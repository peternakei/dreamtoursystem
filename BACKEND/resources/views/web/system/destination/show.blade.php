@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('destinations.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Destination Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @if ($destination->is_active)
                            @can('create-destination-facts')
                                <a href="#" data-bs-toggle="modal" data-bs-target="#create-destination-fact-modal"
                                    class="btn btn-primary">Create Facts</a>
                            @endcan
                            @can('assign-destination-categories')
                                @if (count($destination->facts) > 0)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#assign-destination-category-modal"
                                        class="btn btn-success">Assign Categories</a>
                                @endif
                            @endcan
                            @can('assign-destination-activities')
                                @if (count($destination->categories) > 0)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#assign-destination-activity-modal"
                                        class="btn btn-info">Assign Activity</a>
                                @endif
                            @endcan
                            @can('upload-destination-images')
                                @if (count($destination->activities) > 0)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#upload-destination-images-modal"
                                        class="btn btn-success">Upload Images</a>
                                @endif
                            @endcan
                        @endif
                        @can('change-destination-status')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#change-destination-status-modal"
                                class="btn btn-danger">Change Status</a>
                        @endcan
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
                                        <a href="#facts" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Facts</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#activities" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Activities</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#categories" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Categories</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#images" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Images</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="facts">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($destination->facts) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Fact</th>
                                                                {{-- <th>Sub Fact</th> --}}
                                                                <th>Description</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($destination->facts as $fact)
                                                                <tr>
                                                                    <td>{{ $fact->fact }}</td>
                                                                    {{-- <td>{{ $fact->sub_fact }}</td> --}}
                                                                    <td>{!! html_entity_decode($fact->description) !!}</td>
                                                                    <td class="table-action text-start">
                                                                        <a href="javascript:void(0);"
                                                                            onclick="getDestinationFact('{{ $destination->uuid }}','{{ $fact->uuid }}');"
                                                                            class="action-icon"> <i
                                                                                class="mdi mdi-book-edit"></i></a>
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
                                                                    Fact Created!</h5>
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
                                                @if (count($destination->activities) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Activity</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($destination->activities as $activity)
                                                                <tr>
                                                                    <td>{{ $activity->activity->name }}</td>
                                                                    <td><span class="badge"
                                                                            style="background-color: @if ($activity->is_active) green @else red @endif">
                                                                            @if ($activity->is_active)
                                                                                Active
                                                                            @else
                                                                                Inactive
                                                                            @endif
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $activity->created_at }}</td>
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
                                                                    Activity Registered!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="categories">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($destination->categories) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Category</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($destination->categories as $category)
                                                                <tr>
                                                                    <td>{{ $category->category->name }}</td>
                                                                    <td><span class="badge"
                                                                            style="background-color: green;">
                                                                            Active
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $category->created_at }}</td>
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
                                                                    Category Registered!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="images">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($destination->images) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Image</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($destination->images as $image)
                                                                <tr>
                                                                    <td>{{ $image->name }}</td>
                                                                    <td><span class="badge"
                                                                            style="background-color: @if ($image->is_active) green @else red @endif">
                                                                            @if ($image->is_active)
                                                                                Active
                                                                            @else
                                                                                Inactive
                                                                            @endif
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $image->created_at }}</td>
                                                                    <td class="table-action text-start">
                                                                        <a href="javascript:void(0);"
                                                                            onclick="getDestinationImage('{{ $image->uuid }}');"
                                                                            class="action-icon"> <i
                                                                                class="mdi mdi-eye"></i></a>
                                                                        <a href="javascript:void(0);"
                                                                            onclick="getDeleteDestinationImage('{{ $destination->uuid }}','{{ $image->uuid }}');"
                                                                            class="action-icon text-danger"> <i
                                                                                class="mdi mdi-delete"></i></a>
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
                                                                    Image uploaded!</h5>
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
                        style="border-bottom: 5px @if ($destination->is_active == true) green @else crimson @endif solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: @if ($destination->is_active == true) green @else crimson @endif"></i>
                                <h5 class="text-center"
                                    style="color: @if ($destination->is_active == true) green @else crimson @endif; font-size: 1.2em; font-weight: bold;">
                                    @if ($destination->is_active == true)
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
                                <p>{{ $destination->description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">Destination Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Name</h5>
                                        <h5 style="font-weight: 500;">{!! $destination->name !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Location</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->location?->name ?? '—' }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Region</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->region?->name ?? '—' }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Country</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->region?->country?->name ?? '—' }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Latitudes</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->latitude }}</h5>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Longitudes</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->longitude }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $destination->createdBy?->userProfile?->name ?? '—' }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $destination->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $destination->updatedBy?->userProfile?->name ?? '—' }}
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
    @include('web.system.destination.includes.change_destination_status_modal')
    @include('web.system.destination.includes.upload_destination_images_modal')
    @include('web.system.destination.includes.view_destination_image_modal')
    @include('web.system.destination.includes.delete_destination_image_modal')
    @include('web.system.destination.includes.assign_destination_activity_modal')
    @include('web.system.destination.includes.create_destination_facts_modal')
    @include('web.system.destination.includes.edit_destination_fact_details_modal')
    @include('web.system.destination.includes.assign_destination_categories_modal')
@endsection
@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @include('web.system.destination.includes.scripts.script')
@endsection
