@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
                    <p class="text-muted mb-0">
                        Reusable building blocks for every quote you build. Edit a destination, accommodation,
                        activity or vehicle once — it auto-fills on every proposal that uses it.
                    </p>
                </div>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Library</a></li>
                        <li class="breadcrumb-item active">{{ $sub_title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        @foreach ($entities as $entity)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100" style="border-top: 4px solid {{ $entity['color'] }};">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3" style="gap: 12px;">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 44px; height: 44px; background-color: {{ $entity['color'] }}1a; color: {{ $entity['color'] }};">
                                <i class="{{ $entity['icon'] }}" style="font-size: 1.4em;"></i>
                            </span>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $entity['label'] }}</h5>
                                <small class="text-muted">{{ $entity['description'] }}</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div style="font-size: 1.6em; font-weight: 700; line-height: 1;">
                                    {{ $entity['total'] }}
                                </div>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">{{ $entity['active'] }} active</span>
                                @if ($entity['inactive'] > 0)
                                    <span class="badge bg-secondary">{{ $entity['inactive'] }} inactive</span>
                                @endif
                            </div>
                        </div>

                        @if ($entity['index_route'])
                            <a href="{{ route($entity['index_route']) }}"
                                class="btn btn-sm w-100"
                                style="background-color: {{ $entity['color'] }}; color: #fff;">
                                Manage {{ $entity['label'] }} <i class="uil uil-arrow-right"></i>
                            </a>
                        @else
                            <button class="btn btn-sm w-100 btn-light" disabled>
                                Read-only (admin pending)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-information-outline me-1"></i> How the Library works
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="mb-1">1. Build it once</h6>
                            <small class="text-muted">
                                Add a destination, lodge or activity here, with description, images and videos.
                                The library is the single source of truth.
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="mb-1">2. Pick it on any quote</h6>
                            <small class="text-muted">
                                In the quote builder, select an accommodation from the Library dropdown
                                (filtered by destination automatically). Description and images flow into the day card.
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="mb-1">3. Override per quote when needed</h6>
                            <small class="text-muted">
                                Custom name, custom notes — set them on the day if a guest needs special handling.
                                The library copy stays untouched.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
