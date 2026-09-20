@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('vehicles.index') }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">{{ $title }}</h4>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Library</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Vehicles</a></li>
                        <li class="breadcrumb-item active">{{ $vehicle->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Vehicle Details</h5>
                    <form method="POST" action="{{ route('vehicles.update', $vehicle->uuid) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $vehicle->name) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="text" name="capacity" class="form-control"
                                value="{{ old('capacity', $vehicle->capacity) }}" maxlength="80">
                            <small class="text-muted">e.g. "Up to 6 guests"</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Short summary (the long-form description lives in the Description tab on the right)">{{ old('description', $vehicle->description) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-content-save"></i> Save Details
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Status</small>
                    <span class="badge"
                        style="background-color: {{ $vehicle->is_active ? 'green' : 'orangered' }}">
                        {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <form method="POST" action="{{ route('vehicles.change_status', $vehicle->uuid) }}"
                        class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            Toggle
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <x-library.media-tabs :entity="$vehicle" type="vehicles" :showHeader="false" />
                </div>
            </div>
        </div>
    </div>
@endsection
