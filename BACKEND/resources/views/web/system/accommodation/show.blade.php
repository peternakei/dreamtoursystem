@extends('layouts.app')
@section('content')
    @php
        $linkedDestinationIds = $accommodation->destinations->pluck('id')->all();
    @endphp
    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('accommodations.index') }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">{{ $title }}</h4>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Library</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('accommodations.index') }}">Accommodations</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $accommodation->name }}</li>
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
                    <h5 class="card-title">Accommodation Details</h5>
                    <form method="POST" action="{{ route('accommodations.update', $accommodation->uuid) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required maxlength="200"
                                value="{{ old('name', $accommodation->name) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stay Type</label>
                            <select name="stay_type_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach ($stay_types as $st)
                                    <option value="{{ $st->id }}"
                                        @selected(old('stay_type_id', $accommodation->stay_type_id) == $st->id)>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Primary Destination</label>
                            <select name="primary_destination_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach ($destinations as $d)
                                    <option value="{{ $d->id }}"
                                        @selected(old('primary_destination_id', $accommodation->primary_destination_id) == $d->id)>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Also available in</label>
                            <select name="destination_ids[]" class="form-select" multiple size="8">
                                @foreach ($destinations as $d)
                                    <option value="{{ $d->id }}"
                                        @selected(in_array($d->id, $linkedDestinationIds))>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to pick multiple.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location (free text)</label>
                            <input type="text" name="location_text" class="form-control" maxlength="200"
                                value="{{ old('location_text', $accommodation->location_text) }}"
                                placeholder="e.g. Inside Central Serengeti National Park">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="The full description lives in the Description tab on the right.">{{ old('description', $accommodation->description) }}</textarea>
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
                        style="background-color: {{ $accommodation->is_active ? 'green' : 'orangered' }}">
                        {{ $accommodation->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <form method="POST"
                        action="{{ route('accommodations.change_status', $accommodation->uuid) }}"
                        class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Toggle</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <x-library.media-tabs :entity="$accommodation" type="accommodations" :showHeader="false" />
                </div>
            </div>
        </div>
    </div>
@endsection
