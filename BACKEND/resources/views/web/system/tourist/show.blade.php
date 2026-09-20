@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('tourists.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Tourist Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#change-tourist-status-modal"
                            class="btn btn-danger">Change Status</a>
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
                                        <a href="#trips" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Trips</span>
                                        </a>
                                    </li>

                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="trips">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                @if (count($tourist->trips) > 0)
                                                    <table class="table table-bordered table-centered mb-0">
                                                        <thead style="background-color: #e9ecef;">
                                                            <tr>
                                                                <th>Booking No</th>
                                                                <th>Booking Date</th>
                                                                <th>From</th>
                                                                <th>To</th>
                                                                <th>Type</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($tourist->trips as $booking)
                                                                <tr>
                                                                    <td>{{ $booking->booking_number }}</td>
                                                                    <td>{{ $booking->booking_date }}</td>
                                                                    <td>{{ $booking->trip->from_date }}
                                                                        {{ $booking->trip->to_date }}</td>
                                                                    <td>{{ $booking->trip->tripType->name }}
                                                                        </td>
                                                                    <td><span class="badge"
                                                                            style="background-color: {{ $booking->status->color }}">
                                                                            {{ $booking->status->name }}
                                                                        </span>
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
                                                                    Booking Created!</h5>
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
                        style="border-bottom: 5px @if ($tourist->is_active == true) green @else crimson @endif solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: @if ($tourist->is_active == true) green @else crimson @endif"></i>
                                <h5 class="text-center"
                                    style="color: @if ($tourist->is_active == true) green @else crimson @endif; font-size: 1.2em; font-weight: bold;">
                                    @if ($tourist->is_active == true)
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
                                <h5 style="font-weight: 500; font-size: 1.2em;">Tourist Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Name</h5>
                                        <h5 style="font-weight: 500;">{!! $tourist->name !!}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Phone</h5>
                                        <h5 style="font-weight: 500;">{{ $tourist->phone }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Email</h5>
                                        <h5 style="font-weight: 500;">{{ $tourist->email }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Country</h5>
                                        <h5 style="font-weight: 500;">{{ $tourist->country->name }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Address</h5>
                                        <h5 style="font-weight: 500;">{{ $tourist->address }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $tourist->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $tourist->createdBy->userProfile->name ?? '' }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $tourist->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            @if (isset($tourist->updatedBy))
                                                {{ $tourist->updatedBy->userProfile->name }}
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
    @include('web.system.tourist.includes.change_tourist_status_modal')
@endsection
@section('script')
    @include('web.system.tourist.includes.scripts.script')
@endsection
