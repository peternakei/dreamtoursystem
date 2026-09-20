@extends('layouts.app')

@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('inquiries.show', $inquiry->uuid) }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="page-title mb-1" style="font-size: 1.6em;">Create Quotation Builder</h4>
                    <p class="text-muted mb-0">Start from this inquiry and let the system pull trip imagery, itinerary structure, and pricing defaults from your existing safari library.</p>
                </div>
                <div class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">
                    Request {{ $inquiry->request_reference ?? $inquiry->inquiry_code }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('quotations.store_from_inquiry', $inquiry->uuid) }}" method="POST">
        @csrf
        <div class="row mt-3">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3" style="font-weight: 600;">Inquiry Snapshot</h5>
                        <div class="mb-3">
                            <div class="text-muted small">Guest</div>
                            <div class="fw-semibold">{{ $inquiry->tourist->name ?? 'N/A' }}</div>
                            <div class="text-muted">{{ $inquiry->tourist->email ?? 'No email' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Travel Window</div>
                            <div class="fw-semibold">
                                {{ optional($inquiry->from_date)->format('M d, Y') ?? 'TBD' }}
                                to
                                {{ optional($inquiry->to_date)->format('M d, Y') ?? 'TBD' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Guests</div>
                            <div class="fw-semibold">{{ $inquiry->guests ?? 1 }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Trip Type</div>
                            <div class="fw-semibold">{{ $inquiry->tripType->name ?? 'Not set' }}</div>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted small">Request Notes</div>
                            <div>{{ $inquiry->client_message ?? $inquiry->description ?? 'No notes provided.' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3" style="font-weight: 600;">Proposal Defaults</h5>
                        <div class="mb-3">
                            <label class="form-label">Proposal Title</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $inquiry->tour_title ?: 'Tailor-Made Safari Proposal') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control"
                                value="{{ old('subtitle', 'Prepared for ' . ($inquiry->tourist->name ?? 'your guest')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency</label>
                            <select name="currency_id" class="form-select">
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected(old('currency_id', $defaultCurrencyId) == $currency->id)>
                                        {{ $currency->name }} ({{ $currency->short_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service Class</label>
                            <select name="service_class_id" class="form-select">
                                <option value="">Use inquiry preference</option>
                                @foreach ($serviceClasses as $serviceClass)
                                    <option value="{{ $serviceClass->id }}" @selected(old('service_class_id', $inquiry->service_class_id) == $serviceClass->id)>{{ $serviceClass->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Introduction</label>
                            <textarea name="introduction" rows="5" class="form-control">{{ old('introduction') }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="internal_notes" rows="4" class="form-control">{{ old('internal_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <div>
                                <h5 class="mb-1" style="font-weight: 600;">Choose a Trip Template</h5>
                                <p class="text-muted mb-0">Pick the closest trip and the builder will prefill days, imagery, included items, and budget-based starting prices.</p>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="trip_id" id="trip_none" value="" checked>
                                <label class="form-check-label" for="trip_none">Start from scratch</label>
                            </div>
                        </div>

                        <div class="row">
                            @forelse ($trips as $trip)
                                @php
                                    $banner = $trip->banners->first();
                                    $bannerUrl = $banner ? asset('storage/uploads/' . $banner->name) : null;
                                    $matchedDestinations = collect($inquiry->destinations ?? [])->filter(function ($destinationId) use ($trip) {
                                        return $trip->destinations->contains('destination_id', $destinationId);
                                    })->count();
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <label class="card h-100 border shadow-sm quotation-trip-option" style="cursor: pointer;">
                                        <div class="position-relative">
                                            @if ($bannerUrl)
                                                <img src="{{ $bannerUrl }}" alt="{{ $trip->name }}" class="w-100 rounded-top" style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="rounded-top d-flex align-items-center justify-content-center text-white" style="height: 200px; background: linear-gradient(135deg, #0f5d4b, #c9862f);">
                                                    <div class="text-center px-3">
                                                        <div class="mb-2" style="font-size: 2rem;"><i class="uil uil-map"></i></div>
                                                        <div class="fw-semibold">{{ $trip->name }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="position-absolute top-0 end-0 m-3">
                                                <input type="radio" class="form-check-input quotation-trip-radio" name="trip_id"
                                                    value="{{ $trip->uuid }}" @checked(old('trip_id') == $trip->uuid)>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="mb-1">{{ $trip->name }}</h5>
                                                    <small class="text-muted">{{ $trip->tripType->name ?? 'Trip' }}</small>
                                                </div>
                                                <span class="badge bg-light text-dark">{{ $matchedDestinations }} match{{ $matchedDestinations == 1 ? '' : 'es' }}</span>
                                            </div>
                                            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($trip->description ?? 'No description available.'), 120) }}</p>
                                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                                <span class="badge bg-primary-subtle text-primary-emphasis">{{ $trip->duration_days ?? $trip->destinations->count() ?: 1 }} days</span>
                                                <span class="badge bg-success-subtle text-success-emphasis">{{ $trip->destinations->count() }} destinations</span>
                                                <span class="badge bg-warning-subtle text-warning-emphasis">{{ $trip->addons->count() }} addons</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-light border mb-0">
                                        No trip templates match this inquiry yet. You can still start from scratch and build the quote manually.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="uil uil-rocket me-1"></i> Launch Quote Builder
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
