@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('trips.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Trip Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @can('add-trip-addons')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-addon-modal"
                                class="btn btn-success">Add Trip Addons</a>
                        @endcan
                        @can('assign-trip-category')
                            @if (count($trip->addons) > 0)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-category-modal"
                                    class="btn btn-info">Assign Trip Categories</a>
                            @endif
                        @endcan
                        @can('assign-trip-category-activities')
                            @if (count($trip->categories) > 0)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-category-activities-modal"
                                    class="btn btn-primary">Assign Trip Category Activities</a>
                            @endif
                        @endcan
                        @can('assign-trip-destination')
                            @if (count($trip->categoryActivities) > 0)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-destination-modal"
                                    class="btn btn-danger">Assign Trip Destinations</a>
                            @endif
                        @endcan
                        @if ($trip->tripType->id == 2)
                            @can('add-trip-group')
                                @if (count($trip->destinations) > 0)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-group-modal"
                                        class="btn btn-success">Add Trip Group</a>
                                @endif
                            @endcan
                            @can('add-trip-group-camps')
                                @if (count($trip->groups) > 0)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-group-camp-modal"
                                        class="btn btn-info">Add Trip Group Camps</a>
                                @endif
                            @endcan
                        @endif
                        @can('add-trip-points')
                            @if (count($trip->destinations) > 0)
                                <a href="{{ route('trips.planner.edit', $trip->uuid) }}"
                                    class="btn btn-outline-dark">Trip Planner</a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-point-modal"
                                    class="btn btn-primary">Add Trip Points</a>
                            @endif
                        @endcan
                        @can('add-trip-points')
                            @if (count($trip->points) > 0)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#upload-trip-banner-modal"
                                    class="btn btn-success">Add Trip Banner</a>
                            @endif
                        @endcan
                        <!-- @can('add-trip-price')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#create-trip-price-modal"
                                class="btn btn-dark">Manage Trip Prices</a>
                        @endcan -->
                        @can('create-budgets')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#manage-trip-budgets-modal"
                                class="btn btn-secondary">Manage Budgets</a>
                        @endcan
                        @can('change-trip-status')
                            @if (count($trip->banners) > 0 && $trip->is_published == false && count($trip->budgets()->get()) > 0)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#publish-trip-modal"
                                    class="btn btn-warning">Publish Trip</a>
                            @endif
                        @endcan
                        @if ($trip->is_published)
                            <a href="#" data-bs-toggle="modal" data-bs-target="#apply-trip-to-quote-modal"
                                class="btn" style="background-color: #FFA319; color: #fff;">
                                <i class="mdi mdi-file-document-edit"></i> Apply to Quote
                            </a>
                        @endif
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
                                        <a href="#destinations" data-bs-toggle="tab" aria-expanded="true"
                                            class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Destinations</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#addons" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Includes</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#addons_not" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Not Includes</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#categories" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Categories</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#activities" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Activities</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#points" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Points</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="destinations">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($trip->destinations) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($trip->destinations as $destination)
                                                                <tr>
                                                                    <td>{{ $destination->destination->name }}</td>
                                                                    <td>{{ $destination->description }}</td>
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
                                                                    Destination Created!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="addons">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($trip->addons) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Addon</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($trip->addons as $addon)
                                                                @if ($addon->addon->is_include)
                                                                    <tr>
                                                                        <td>{{ $addon->addon->name }}</td>
                                                                        <td><span class="badge"
                                                                                style="background-color: @if ($addon->is_active) green @else red @endif">
                                                                                @if ($addon->is_active)
                                                                                    Active
                                                                                @else
                                                                                    Inactive
                                                                                @endif
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $addon->created_at }}</td>
                                                                        <td>
                                                                            {{-- @can('delete-trip-addon') --}}
                                                                                <a href="#"
                                                                                    onclick="deleteTripAddon('{{ $addon->uuid }}')"
                                                                                    class="action-icon text-danger">
                                                                                    <i class="uil uil-trash"></i>
                                                                                </a>
                                                                            {{-- @endcan --}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
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
                                                                    Addon Registered!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="addons_not">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($trip->addons) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Addon</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($trip->addons as $addon)
                                                                @if (!$addon->addon->is_include)
                                                                    <tr>
                                                                        <td>{{ $addon->addon->name }}</td>
                                                                        <td><span class="badge"
                                                                                style="background-color: @if ($addon->is_active) green @else red @endif">
                                                                                @if ($addon->is_active)
                                                                                    Active
                                                                                @else
                                                                                    Inactive
                                                                                @endif
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $addon->created_at }}</td>
                                                                        <td>
                                                                            {{-- @can('delete-trip-addon') --}}
                                                                            <a href="#"
                                                                                onclick="deleteTripAddon('{{ $addon->uuid }}')"
                                                                                class="action-icon text-danger">
                                                                                <i class="uil uil-trash"></i>
                                                                            </a>
                                                                            {{-- @endcan --}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
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
                                                                    Addon Registered!</h5>
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
                                                @if (count($trip->categories) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Category</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($trip->categories as $category)
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
                                    <div class="tab-pane show" id="activities">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($trip->categoryActivities) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Category</th>
                                                                <th>Activity</th>
                                                                <th>Description</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($trip->categoryActivities as $activity)
                                                                <tr>
                                                                    <td>{{ $activity->tripCategory->category->name }}</td>
                                                                    <td>{{ $activity->activity->name }}</td>
                                                                    <td>{{ $activity->description }}</td>
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
                                    <div class="tab-pane show" id="points">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($trip->points) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Title</th>
                                                                <th>Description</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($trip->points as $point)
                                                                <tr>
                                                                    <td>{{ $point->title }}</td>
                                                                    <td>{{ $point->description }}</td>
                                                                    <td><a href="javascript:void(0);"
                                                                        onclick="getPointEditDetails('{{ $point->uuid }}');"
                                                                        class="action-icon text-primary"><i
                                                                            class="uil uil-edit"></i>
                                                                    </a></td>
                                                                    <td>
                                                                        {{-- @can('delete-trip-point') --}}
                                                                        <a href="#"
                                                                            onclick="deleteTripPoint('{{ $point->uuid }}')"
                                                                            class="action-icon text-danger">
                                                                            <i class="uil uil-trash"></i>
                                                                        </a>
                                                                        {{-- @endcan --}}
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
                                                                    Point Registered!</h5>
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
                    @if ($trip->trip_type_id == 2)
                        <div class="card mt-2">
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified nav-bordered mb-3">
                                        <li class="nav-item">
                                            <a href="#groups" data-bs-toggle="tab" aria-expanded="true"
                                                class="nav-link active">
                                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                                <span class="d-none d-md-block">Groups</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#camps" data-bs-toggle="tab" aria-expanded="true"
                                                class="nav-link">
                                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                                <span class="d-none d-md-block">Camps</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="groups">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12" data-simplebar
                                                    style="max-height: 350px;">
                                                    @if (count($trip->groups) > 0)
                                                        <table class="table table-bordered table-centered mb-0">
                                                            <thead style="background-color: #e9ecef;">
                                                                <tr>
                                                                    <th>Group</th>
                                                                    <th>Size</th>
                                                                    <th>Days</th>
                                                                    <th>Description</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($trip->groups as $group)
                                                                    <tr>
                                                                        <td>{{ $group->group }}</td>
                                                                        <td>{{ $group->size }}
                                                                        <td>{{ $group->days }}</td>
                                                                        <td>{{ $group->description }}</td>
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
                                                                        Group Created!</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane show" id="camps">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12" data-simplebar
                                                    style="max-height: 350px;">
                                                    @if (count($trip->groupCamps) > 0)
                                                        <table class="table table-bordered table-centered mb-0">
                                                            <thead style="background-color: #e9ecef;">
                                                                <tr>
                                                                    <th>Group</th>
                                                                    <th>Camp</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($trip->groupCamps as $camp)
                                                                    <tr>
                                                                        <td>{{ $camp->tripGroup->group }}</td>
                                                                        <td>{{ $camp->camp }}</td>
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
                                                                        Camp Registered!</h5>
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
                    @endif
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 class="text-left" style="font-size: 1.2em; font-weight: bold;">Trip Banner</h5>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        @if (isset($banner->name))
                                            <img src="{{ asset('storage/uploads/' . $banner->name) }}"
                                                style="width: 100%; height: auto" alt="" srcset="">
                                        @else
                                            <h5 class="text-center">No trip banner image uploaded!</h5>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card" style="border-bottom: 5px {{ $trip->tripStatus?->color ?? '#94a3b8' }};">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: {{ $trip->tripStatus?->color ?? '#94a3b8' }}"></i>
                                <h5 class="text-center"
                                    style="color: {{ $trip->tripStatus?->color ?? '#94a3b8' }}; font-size: 1.2em; font-weight: bold;">
                                    {{ $trip->tripStatus?->name ?? '—' }}
                                </h5>
                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <p>{!! $trip->description !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">Trip Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Name</h5>
                                        <h5 style="font-weight: 500;">{!! $trip->name !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Type</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->tripType?->name ?? '—' }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Source</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->tripSource?->name ?? '—' }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">From Date</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->from_date }}</h5>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">To Date</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->to_date }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Duration Days</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->duration_days ?? $trip->tripDays()->count() }}</h5>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Duration Nights</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->duration_nights ?? max($trip->tripDays()->count() - 1, 0) }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h5 class="opacity-75" style="font-weight: 500;">Last Booking Date</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->last_booking_date }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Last Payment Date</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $trip->createdBy?->userProfile?->name ?? '—' }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $trip->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            @if (isset($trip->updatedBy))
                                                {{ $trip->updatedBy?->userProfile?->name ?? '—' }}
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 style="font-weight: 500; font-size: 1.2em;">Budget Snapshot</h5>
                                    <small class="text-muted">Active rows only</small>
                                </div>
                                @php
                                    $activeBudgets = $trip->budgets->where('is_active', true)->sortBy(function ($budget) {
                                        return sprintf('%05d-%05d-%05d', $budget->season_id, $budget->service_class_id, $budget->quantity);
                                    });
                                @endphp
                                @if ($activeBudgets->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead style="background-color: #e9ecef;">
                                                <tr>
                                                    <th>Season</th>
                                                    <th>Class</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($activeBudgets as $budget)
                                                    <tr>
                                                        <td>{{ $budget->season->name }}</td>
                                                        <td>{{ $budget->serviceClass->name }}</td>
                                                        <td>{{ $budget->quantity }}</td>
                                                        <td>{{ $budget->currency->short_name }} {{ number_format($budget->price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="mb-0 text-muted">No active budgets configured yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 style="font-weight: 500; font-size: 1.2em;">Trip Price Snapshot</h5>
                                    <small class="text-muted">Operational prices</small>
                                </div>
                                @php
                                    $activePrices = $trip->prices->where('is_active', true);
                                @endphp
                                @if ($activePrices->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead style="background-color: #e9ecef;">
                                                <tr>
                                                    <th>Age</th>
                                                    <th>Scope</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($activePrices as $price)
                                                    <tr>
                                                        <td>{{ $price->ageGroup->name ?? 'N/A' }}</td>
                                                        <td>{{ $price->tripGroup->group ?? 'Whole Trip' }}</td>
                                                        <td>{{ $price->currency->short_name }} {{ number_format($price->price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="mb-0 text-muted">No trip prices configured yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('web.system.trip.includes.change_trip_status_modal')
    @include('web.system.trip.includes.add_trip_addon_modal')
    @include('web.system.trip.includes.assign_trip_category_modal')
    @include('web.system.trip.includes.assign_trip_destination_modal')
    @include('web.system.trip.includes.assign_trip_category_activities_modal')
    @include('web.system.trip.includes.add_trip_points_modal')
    @include('web.system.trip.includes.edit_trip_point_details_modal')
    @include('web.system.trip.includes.add_trip_group_modal')
    @include('web.system.trip.includes.add_trip_group_camp_modal')
    @include('web.system.trip.includes.add_trip_price_modal')
    @include('web.system.trip.includes.manage_trip_budgets_modal')
    @include('web.system.trip.includes.publish_trip_modal')
    @include('web.system.trip.includes.upload_trip_banner_modal')

    @include('web.system.trip.includes.delete_trip_point_modal')
    @include('web.system.trip.includes.delete_trip_addon_modal')

    {{-- Apply this trip to an inquiry → creates a new QuotationVersion --}}
    @if ($trip->is_published)
        <div class="modal fade" id="apply-trip-to-quote-modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('trips.apply_to_inquiry', $trip->uuid) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Apply "{{ $trip->name }}" to a quote</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="small text-muted mb-3">
                                Pick the guest inquiry to base this quote on. The new quote will land in your builder
                                pre-seeded with this trip's days, destinations, terms, and prices.
                            </p>
                            @if ($recentInquiries->isEmpty())
                                <div class="alert alert-warning mb-0">
                                    No inquiries yet. Create one at
                                    <a href="{{ route('inquiries.index') }}">/inquiries</a> first.
                                </div>
                            @else
                                <div class="mb-3">
                                    <label class="form-label">Inquiry *</label>
                                    <select name="inquiry_uuid" class="form-select" required>
                                        <option value="">— Pick an inquiry —</option>
                                        @foreach ($recentInquiries as $inq)
                                            <option value="{{ $inq->uuid }}">
                                                {{ $inq->tourist->name ?? 'Unknown guest' }}
                                                @if ($inq->tour_title) — {{ $inq->tour_title }}@endif
                                                ({{ optional($inq->created_at)->format('Y-m-d') ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" {{ $recentInquiries->isEmpty() ? 'disabled' : '' }}>
                                <i class="mdi mdi-check"></i> Create Quote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('script')
    @include('web.system.trip.includes.scripts.script')
@endsection
