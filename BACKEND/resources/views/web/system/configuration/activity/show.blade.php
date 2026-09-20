@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('activities.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Activity Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @can('create-activity-prices')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#create-activity-price-modal"
                                class="btn btn-info">Create Price</a>
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
                                        <a href="#prices" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                            <i class="mdi mdi-currency-usd d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Prices</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#library-media" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                            <i class="mdi mdi-image-multiple d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Library Media</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="prices">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($activity->prices) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Age Group</th>
                                                                <th>Duration</th>
                                                                <th>Price</th>
                                                                <th>Status</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($activity->prices as $price)
                                                                <tr>
                                                                    <td>{{ $price->ageGroup->name }}</td>
                                                                    <td>{{ $price->duration }} {{ $price->durationType->name }}</td>
                                                                    <td>{{ $price->currency->short_name }} {{ number_format($price->price) }}</td>
                                                                    <td><span class="badge"
                                                                            style="background-color: @if ($price->is_active) green @else red @endif">
                                                                            @if ($price->is_active)
                                                                                Active
                                                                            @else
                                                                                Inactive
                                                                            @endif
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $price->created_at }}</td>
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
                                                                    Price Created!</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="library-media">
                                        <x-library.media-tabs :entity="$activity" type="activities" :showHeader="false" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card"
                        style="border-bottom: 5px @if ($activity->is_active == true) green @else crimson @endif solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: @if ($activity->is_active == true) green @else crimson @endif"></i>
                                <h5 class="text-center"
                                    style="color: @if ($activity->is_active == true) green @else crimson @endif; font-size: 1.2em; font-weight: bold;">
                                    @if ($activity->is_active == true)
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
                                <p>{{ $activity->description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">Activity Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Name</h5>
                                        <h5 style="font-weight: 500;">{!! $activity->name !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $activity->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $activity->createdBy->userProfile->name }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $activity->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            @if (isset($activity->updatedBy))
                                                {{ $activity->updatedBy->userProfile->name }}
                                            @endif
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
    @include('web.system.configuration.activity.includes.create_new_activity_price_modal')
@endsection
@section('script')
    @include('web.system.configuration.activity.includes.scripts.script')
@endsection
