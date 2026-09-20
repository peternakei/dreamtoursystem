@extends('layouts.app')

@section('content')
    @php
        $currencyCode = $version->currency->short_name ?? 'USD';
        $includedAddons = $includedAddons ?? collect();
        $excludedAddons = $excludedAddons ?? collect();
        $selectedIncludedAddonIds = $selectedIncludedAddonIds ?? [];
        $selectedExcludedAddonIds = $selectedExcludedAddonIds ?? [];
    @endphp

    <style>
        .quote-builder-hero {
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(172, 85, 38, 0.96), rgba(0, 151, 220, 0.92)),
                #AC5526;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .quote-builder-hero::after {
            content: "";
            position: absolute;
            inset: auto -40px -60px auto;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .builder-card {
            border: 1px solid rgba(15, 93, 75, 0.08);
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(23, 52, 45, 0.06);
        }

        .builder-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #17342d;
        }

        .builder-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #AC5526;
            color: #fff;
            font-weight: 700;
        }

        .builder-repeater-item {
            border: 1px solid #e7ecea;
            border-radius: 16px;
            background: #fcfdfd;
        }

        .builder-sidebar {
            position: sticky;
            top: 90px;
        }

        .builder-kpi {
            border-radius: 16px;
            background: #f6f8f7;
            padding: 14px 16px;
        }

        .builder-kpi .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0.05em;
        }

        .builder-kpi .value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0097DC;
        }

        .builder-quick-link {
            text-decoration: none;
            display: block;
            padding: 12px 14px;
            border-radius: 14px;
            background: #f7f3ec;
            color: #0097DC;
            font-weight: 600;
        }
    </style>

    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('quotations.show', $version->quotation->uuid) }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="quote-builder-hero p-4 p-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-8 position-relative" style="z-index: 1;">
                        <span class="badge bg-light text-dark mb-3">{{ $version->reference_number }}</span>
                        <h2 class="mb-2" style="font-size: 2rem;">{{ $version->title }}</h2>
                        <p class="mb-3" style="max-width: 700px; opacity: 0.9;">
                            Build a polished guest-facing proposal with automatic safari imagery, destination-led day cards, and price lines tied to your existing budget matrix.
                        </p>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            <span class="badge bg-dark-subtle text-dark">{{ $version->quotation->tourist->name ?? 'Guest' }}</span>
                            <span class="badge bg-dark-subtle text-dark">{{ $version->guest_count }} traveler{{ $version->guest_count == 1 ? '' : 's' }}</span>
                            <span class="badge bg-dark-subtle text-dark">{{ $version->duration_days ?: $version->days->count() ?: 1 }} days</span>
                            <span class="badge bg-dark-subtle text-dark">{{ $currencyCode }} {{ number_format($version->total_amount ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-lg-4 position-relative mt-4 mt-lg-0" style="z-index: 1;">
                        <div class="d-flex flex-column align-items-lg-end" style="gap: 10px;">
                            <a href="{{ route('quotation_versions.preview', $version->uuid) }}" class="btn btn-light btn-lg">Preview Proposal</a>
                            <form action="{{ route('quotation_versions.pdf.generate', $version->uuid) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-light btn-lg">Generate PDF</button>
                            </form>
                            @if ($version->public_url_enabled)
                                <a href="{{ route('public.itinerary.show', $version->public_token) }}" target="_blank" class="btn btn-link text-white text-decoration-none p-0">
                                    Open public itinerary
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('quotation_versions.update', $version->uuid) }}" method="POST" id="quotation-builder-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row mt-4">
            <div class="col-xl-9">
                <div class="card builder-card" id="builder-overview">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3" style="gap: 12px;">
                            <span class="builder-step">1</span>
                            <div>
                                <div class="builder-section-title">Quote Basics</div>
                                <div class="text-muted">Define the narrative, dates, service level, and key visibility settings.</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" value="{{ old('title', $version->title) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" name="subtitle" value="{{ old('subtitle', $version->subtitle) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Currency</label>
                                <select class="form-select" name="currency_id">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" @selected($version->currency_id == $currency->id)>
                                            {{ $currency->name }} ({{ $currency->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Service Class</label>
                                <select class="form-select" name="service_class_id">
                                    <option value="">Not set</option>
                                    @foreach ($serviceClasses as $serviceClass)
                                        <option value="{{ $serviceClass->id }}" @selected($version->service_class_id == $serviceClass->id)>
                                            {{ $serviceClass->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ optional($version->start_date)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ optional($version->end_date)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Guest Count</label>
                                <input type="number" class="form-control" name="guest_count" min="1" value="{{ old('guest_count', $version->guest_count) }}" required>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">Introduction</label>
                                <textarea class="form-control" rows="4" name="introduction">{{ old('introduction', $version->introduction) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Company Profile / Closing Copy</label>
                                <textarea class="form-control" rows="4" name="company_profile">{{ old('company_profile', $version->company_profile) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Internal Notes</label>
                                <textarea class="form-control" rows="3" name="internal_notes">{{ old('internal_notes', $version->internal_notes) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cover Image</label>
                                <input type="file" class="form-control" name="cover_image" accept="image/*">
                                <div class="form-text">
                                    Used as the proposal hero. Resolution order: trip banner (when this quote is built from a trip)
                                    @if ($version->cover_image_path) → uploaded cover @else → uploaded cover @endif
                                    → first day's destination image.
                                </div>
                                @if ($version->cover_image_path)
                                    <div class="mt-2 d-flex align-items-center" style="gap: 12px;">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($version->cover_image_path) }}" alt="Cover preview" style="height: 64px; width: 96px; object-fit: cover; border-radius: 8px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_cover_image" value="1" id="remove_cover_image">
                                            <label class="form-check-label" for="remove_cover_image">Remove uploaded cover</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card builder-card mt-4" id="builder-days">
                    <div class="card-body p-4">
                        @if ($errors->has('days'))
                            <div class="alert alert-danger" role="alert">{{ $errors->first('days') }}</div>
                        @endif
                        @php $hasAnyDestination = $version->days->contains(fn ($d) => !empty($d->destination_id)); @endphp
                        <div id="destination-warning" class="alert alert-warning {{ $hasAnyDestination ? 'd-none' : '' }}" role="alert">
                            At least one day needs a destination before this quote can be saved.
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <span class="builder-step">2</span>
                                <div>
                                    <div class="builder-section-title">Day-by-Day Itinerary</div>
                                    <div class="text-muted">Each row becomes a polished day card in preview, PDF, and the public itinerary.</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-day-row">
                                <i class="uil uil-plus"></i> Add Day
                            </button>
                        </div>

                        <div id="day-rows" data-next-index="{{ $version->days->count() }}">
                            @forelse ($version->days as $index => $day)
                                <div class="builder-repeater-item p-3 mb-3 day-row" data-row-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Day <span class="day-row-number">{{ $day->day_number ?: ($index + 1) }}</span></h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label">Day No.</label>
                                            <input type="number" min="1" class="form-control day-number-input" name="days[{{ $index }}][day_number]" value="{{ $day->day_number ?: ($index + 1) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="days[{{ $index }}][title]" value="{{ $day->title }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Travel Date</label>
                                            <input type="date" class="form-control" name="days[{{ $index }}][travel_date]" value="{{ optional($day->travel_date)->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Destination</label>
                                            <select class="form-select library-destination-picker" name="days[{{ $index }}][destination_id]">
                                                <option value="">Select destination</option>
                                                @foreach ($destinations as $destination)
                                                    <option value="{{ $destination->id }}" @selected($day->destination_id == $destination->id)>{{ $destination->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Accommodation</label>
                                            <select class="form-select form-select-sm library-accommodation-picker mb-1">
                                                <option value="">— Pick from Library —</option>
                                                @foreach ($accommodations as $a)
                                                    <option value="{{ $a->id }}"
                                                        data-name="{{ $a->name }}"
                                                        data-stay="{{ $a->stayType->name ?? '' }}"
                                                        data-destination-ids="{{ $a->destinations->pluck('id')->implode(',') }}"
                                                        @selected($day->accommodation_id == $a->id)>
                                                        {{ $a->name }}@if ($a->stayType) — {{ $a->stayType->name }}@endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="days[{{ $index }}][accommodation_id]" value="{{ $day->accommodation_id }}" class="library-accommodation-id">
                                            <input type="text" class="form-control form-control-sm library-accommodation-name" name="days[{{ $index }}][accommodation_name]" value="{{ $day->accommodation_name }}" placeholder="Custom name (override library)">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Stay Type</label>
                                            <input type="text" class="form-control library-stay-type" name="days[{{ $index }}][stay_type]" value="{{ $day->stay_type }}" placeholder="Lodge, camp, transit">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Nights</label>
                                            <input type="number" min="0" class="form-control" name="days[{{ $index }}][nights]" value="{{ $day->nights ?? 0 }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Meals</label>
                                            <div class="d-flex flex-wrap mt-2" style="gap: 12px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[{{ $index }}][breakfast]" value="1" @checked($day->breakfast)>
                                                    <label class="form-check-label">Breakfast</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[{{ $index }}][lunch]" value="1" @checked($day->lunch)>
                                                    <label class="form-check-label">Lunch</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[{{ $index }}][dinner]" value="1" @checked($day->dinner)>
                                                    <label class="form-check-label">Dinner</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Accommodation Notes</label>
                                            <textarea class="form-control" rows="3" name="days[{{ $index }}][accommodation_notes]">{{ $day->accommodation_notes }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" rows="3" name="days[{{ $index }}][description]">{{ $day->description }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Activities</label>
                                            @php
                                                $selectedActivityIds = $day->activities->pluck('activity_id')->filter()->all();
                                            @endphp
                                            <select name="days[{{ $index }}][activity_ids][]" class="form-select select2-activities" multiple data-placeholder="Pick from activities library">
                                                @foreach ($activities as $activity)
                                                    <option value="{{ $activity->id }}" @selected(in_array($activity->id, $selectedActivityIds))>{{ $activity->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($activities->isEmpty())
                                                <div class="text-muted small mt-2">No activities configured. Manage them under <a href="{{ url('/activities') }}">Activities</a>.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="builder-repeater-item p-3 mb-3 day-row" data-row-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Day <span class="day-row-number">1</span></h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label">Day No.</label>
                                            <input type="number" min="1" class="form-control day-number-input" name="days[0][day_number]" value="1">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="days[0][title]" value="Arrival Day">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Travel Date</label>
                                            <input type="date" class="form-control" name="days[0][travel_date]">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Destination</label>
                                            <select class="form-select library-destination-picker" name="days[0][destination_id]">
                                                <option value="">Select destination</option>
                                                @foreach ($destinations as $destination)
                                                    <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Accommodation</label>
                                            <select class="form-select form-select-sm library-accommodation-picker mb-1">
                                                <option value="">— Pick from Library —</option>
                                                @foreach ($accommodations as $a)
                                                    <option value="{{ $a->id }}"
                                                        data-name="{{ $a->name }}"
                                                        data-stay="{{ $a->stayType->name ?? '' }}"
                                                        data-destination-ids="{{ $a->destinations->pluck('id')->implode(',') }}">
                                                        {{ $a->name }}@if ($a->stayType) — {{ $a->stayType->name }}@endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="days[0][accommodation_id]" value="" class="library-accommodation-id">
                                            <input type="text" class="form-control form-control-sm library-accommodation-name" name="days[0][accommodation_name]" placeholder="Custom name (override library)">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Stay Type</label>
                                            <input type="text" class="form-control library-stay-type" name="days[0][stay_type]">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Nights</label>
                                            <input type="number" min="0" class="form-control" name="days[0][nights]" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Meals</label>
                                            <div class="d-flex flex-wrap mt-2" style="gap: 12px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[0][breakfast]" value="1">
                                                    <label class="form-check-label">Breakfast</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[0][lunch]" value="1">
                                                    <label class="form-check-label">Lunch</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[0][dinner]" value="1">
                                                    <label class="form-check-label">Dinner</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Accommodation Notes</label>
                                            <textarea class="form-control" rows="3" name="days[0][accommodation_notes]"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" rows="3" name="days[0][description]"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Activities</label>
                                            <select name="days[0][activity_ids][]" class="form-select select2-activities" multiple data-placeholder="Pick from activities library">
                                                @foreach ($activities as $activity)
                                                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($activities->isEmpty())
                                                <div class="text-muted small mt-2">No activities configured. Manage them under <a href="{{ url('/activities') }}">Activities</a>.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card builder-card mt-4" id="builder-pricing">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <span class="builder-step">3</span>
                                <div>
                                    <div class="builder-section-title">Pricing Builder</div>
                                    <div class="text-muted">Use the seeded budget row as your starting point, then add manual or optional items.</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-price-line">
                                <i class="uil uil-plus"></i> Add Price Line
                            </button>
                        </div>

                        <div id="price-line-rows" data-next-index="{{ $version->priceLines->count() }}">
                            @foreach ($version->priceLines as $index => $line)
                                <div class="builder-repeater-item p-3 mb-3 price-line-row" data-row-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Line Item {{ $index + 1 }}</h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Description</label>
                                            <input type="text" class="form-control" name="price_lines[{{ $index }}][description]" value="{{ $line->description }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Traveler Type</label>
                                            @php
                                                $travelerOptions = ['Guest', 'Adult', 'Child', 'Infant', 'Senior', 'Single Supplement'];
                                                if ($line->traveler_type && !in_array($line->traveler_type, $travelerOptions, true)) {
                                                    $travelerOptions[] = $line->traveler_type;
                                                }
                                            @endphp
                                            <select class="form-select select2-pricing-tag" name="price_lines[{{ $index }}][traveler_type]" data-placeholder="Pick or type">
                                                <option value=""></option>
                                                @foreach ($travelerOptions as $option)
                                                    <option value="{{ $option }}" @selected($line->traveler_type === $option)>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" min="1" class="form-control price-qty" name="price_lines[{{ $index }}][quantity]" value="{{ $line->quantity }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit Price</label>
                                            <input type="number" min="0" step="0.01" class="form-control price-unit" name="price_lines[{{ $index }}][unit_price]" value="{{ $line->unit_price }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Total</label>
                                            <input type="number" min="0" step="0.01" class="form-control price-total" name="price_lines[{{ $index }}][total_price]" value="{{ $line->total_price }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Currency</label>
                                            <select class="form-select" name="price_lines[{{ $index }}][currency_id]">
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->id }}" @selected(($line->currency_id ?: $version->currency_id) == $currency->id)>{{ $currency->short_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Source Type</label>
                                            @php
                                                $sourceOptions = ['manual', 'Budget', 'Accommodation', 'Transport', 'Activity', 'Other'];
                                                if ($line->source_type && !in_array($line->source_type, $sourceOptions, true)) {
                                                    $sourceOptions[] = $line->source_type;
                                                }
                                            @endphp
                                            <select class="form-select select2-pricing-tag" name="price_lines[{{ $index }}][source_type]" data-placeholder="Pick or type">
                                                <option value=""></option>
                                                @foreach ($sourceOptions as $option)
                                                    <option value="{{ $option }}" @selected($line->source_type === $option)>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="price_lines[{{ $index }}][source_id]" value="{{ $line->source_id }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Display Options</label>
                                            <div class="d-flex flex-wrap mt-2" style="gap: 18px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="price_lines[{{ $index }}][is_optional]" value="1" @checked($line->is_optional)>
                                                    <label class="form-check-label">Optional</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="price_lines[{{ $index }}][is_visible]" value="1" @checked($line->is_visible)>
                                                    <label class="form-check-label">Visible in quote</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card builder-card mt-4" id="builder-terms">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3" style="gap: 12px;">
                            <span class="builder-step">4</span>
                            <div>
                                <div class="builder-section-title">Inclusions, Exclusions, and Terms</div>
                                <div class="text-muted">Trip addons are already copied in, and you can refine them per version.</div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h6 class="mb-2">Included</h6>
                                <p class="text-muted small mb-2">Pick from your Addons library (items flagged as <em>Included</em>).</p>
                                <select name="included_addon_ids[]" class="form-select select2-addons" multiple data-placeholder="Select inclusions">
                                    @foreach ($includedAddons as $addon)
                                        <option value="{{ $addon->id }}" @selected(in_array($addon->id, $selectedIncludedAddonIds))>{{ $addon->name }}</option>
                                    @endforeach
                                </select>
                                @if ($includedAddons->isEmpty())
                                    <div class="text-muted small mt-2">No addons flagged as included. Manage them under <a href="{{ url('/addons') }}">Addons</a>.</div>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <h6 class="mb-2">Excluded</h6>
                                <p class="text-muted small mb-2">Pick from your Addons library (items flagged as <em>Excluded</em>).</p>
                                <select name="excluded_addon_ids[]" class="form-select select2-addons" multiple data-placeholder="Select exclusions">
                                    @foreach ($excludedAddons as $addon)
                                        <option value="{{ $addon->id }}" @selected(in_array($addon->id, $selectedExcludedAddonIds))>{{ $addon->name }}</option>
                                    @endforeach
                                </select>
                                @if ($excludedAddons->isEmpty())
                                    <div class="text-muted small mt-2">No addons flagged as excluded. Manage them under <a href="{{ url('/addons') }}">Addons</a>.</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Payment Terms</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-payment-term-row">Add</button>
                            </div>
                            <div id="payment-term-rows" data-next-index="{{ $version->paymentTerms->count() }}">
                                @foreach ($version->paymentTerms as $index => $term)
                                    <div class="builder-repeater-item p-3 mb-2 payment-term-row" data-row-index="{{ $index }}">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="payment_terms[{{ $index }}][title]" value="{{ $term->title }}" placeholder="Title">
                                            </div>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" name="payment_terms[{{ $index }}][description]" value="{{ $term->description }}" placeholder="Description">
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="payment_terms[{{ $index }}][is_visible]" value="1" @checked($term->is_visible)>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row">x</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="builder-sidebar">
                    <div class="card builder-card">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Publishing Controls</h5>

                            <div class="builder-kpi mb-3">
                                <div class="label">Current Quote Total</div>
                                <div class="value" id="quote-total-summary">{{ $currencyCode }} {{ number_format($version->total_amount ?? 0, 2) }}</div>
                            </div>

                            <div class="builder-kpi mb-3">
                                <div class="label">Visible Price Lines</div>
                                <div class="value" id="visible-price-lines-summary">{{ $version->priceLines->where('is_visible', true)->count() }}</div>
                            </div>

                            <div class="builder-kpi mb-4">
                                <div class="label">Itinerary Days</div>
                                <div class="value" id="day-count-summary">{{ $version->days->count() ?: 1 }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="vat_enabled" value="1" id="vat_enabled" @checked($version->vat_enabled)>
                                    <label class="form-check-label" for="vat_enabled">Apply VAT (18%)</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="hide_price_breakdown" value="1" id="hide_price_breakdown" @checked($version->hide_price_breakdown)>
                                    <label class="form-check-label" for="hide_price_breakdown">Hide price breakdown</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="hide_total_price" value="1" id="hide_total_price" @checked($version->hide_total_price)>
                                    <label class="form-check-label" for="hide_total_price">Hide total price</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="hide_terms" value="1" id="hide_terms" @checked($version->hide_terms)>
                                    <label class="form-check-label" for="hide_terms">Hide inclusions/exclusions</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="hide_payment_terms" value="1" id="hide_payment_terms" @checked($version->hide_payment_terms)>
                                    <label class="form-check-label" for="hide_payment_terms">Hide payment terms</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="public_url_enabled" value="1" id="public_url_enabled" @checked($version->public_url_enabled)>
                                    <label class="form-check-label" for="public_url_enabled">Enable public itinerary</label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Save Quote Builder</button>
                                <a href="{{ route('quotation_versions.preview', $version->uuid) }}" class="btn btn-outline-success">Preview</a>
                                <button type="submit" form="quoteShareLinkForm" class="btn btn-success">Share Digital Link</button>
                                <button type="submit" form="quoteSharePdfForm" class="btn btn-outline-primary">Share as PDF</button>
                                <a href="{{ route('quotations.show', $version->quotation->uuid) }}" class="btn btn-outline-secondary">Version History</a>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex flex-column" style="gap: 10px;">
                                <a href="#builder-overview" class="builder-quick-link">Quote Basics</a>
                                <a href="#builder-days" class="builder-quick-link">Day-by-Day</a>
                                <a href="#builder-pricing" class="builder-quick-link">Pricing</a>
                                <a href="#builder-terms" class="builder-quick-link">Terms & Payments</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="quoteShareLinkForm" action="{{ route('quotation_versions.share_link', $version->uuid) }}" method="POST" style="display:none;">@csrf</form>
    <form id="quoteSharePdfForm" action="{{ route('quotation_versions.pdf.generate', $version->uuid) }}" method="POST" style="display:none;">@csrf</form>

    <template id="day-row-template">
        <div class="builder-repeater-item p-3 mb-3 day-row" data-row-index="__INDEX__">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Day <span class="day-row-number">__DAY__</span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
            </div>
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Day No.</label>
                    <input type="number" min="1" class="form-control day-number-input" name="days[__INDEX__][day_number]" value="__DAY__">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="days[__INDEX__][title]" value="Day __DAY__">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Travel Date</label>
                    <input type="date" class="form-control" name="days[__INDEX__][travel_date]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Destination</label>
                    <select class="form-select library-destination-picker" name="days[__INDEX__][destination_id]">
                        <option value="">Select destination</option>
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Accommodation</label>
                    <select class="form-select form-select-sm library-accommodation-picker mb-1">
                        <option value="">— Pick from Library —</option>
                        @foreach ($accommodations as $a)
                            <option value="{{ $a->id }}"
                                data-name="{{ $a->name }}"
                                data-stay="{{ $a->stayType->name ?? '' }}"
                                data-destination-ids="{{ $a->destinations->pluck('id')->implode(',') }}">
                                {{ $a->name }}@if ($a->stayType) — {{ $a->stayType->name }}@endif
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="days[__INDEX__][accommodation_id]" value="" class="library-accommodation-id">
                    <input type="text" class="form-control form-control-sm library-accommodation-name" name="days[__INDEX__][accommodation_name]" placeholder="Custom name (override library)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stay Type</label>
                    <input type="text" class="form-control library-stay-type" name="days[__INDEX__][stay_type]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nights</label>
                    <input type="number" min="0" class="form-control" name="days[__INDEX__][nights]" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Meals</label>
                    <div class="d-flex flex-wrap mt-2" style="gap: 12px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="days[__INDEX__][breakfast]" value="1">
                            <label class="form-check-label">Breakfast</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="days[__INDEX__][lunch]" value="1">
                            <label class="form-check-label">Lunch</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="days[__INDEX__][dinner]" value="1">
                            <label class="form-check-label">Dinner</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Accommodation Notes</label>
                    <textarea class="form-control" rows="3" name="days[__INDEX__][accommodation_notes]"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="3" name="days[__INDEX__][description]"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Activities</label>
                    <select name="days[__INDEX__][activity_ids][]" class="form-select select2-activities" multiple data-placeholder="Pick from activities library">
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </template>

    <template id="price-line-template">
        <div class="builder-repeater-item p-3 mb-3 price-line-row" data-row-index="__INDEX__">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Line Item __DAY__</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" name="price_lines[__INDEX__][description]" value="">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Traveler Type</label>
                    <select class="form-select select2-pricing-tag" name="price_lines[__INDEX__][traveler_type]" data-placeholder="Pick or type">
                        <option value=""></option>
                        <option value="Guest" selected>Guest</option>
                        <option value="Adult">Adult</option>
                        <option value="Child">Child</option>
                        <option value="Infant">Infant</option>
                        <option value="Senior">Senior</option>
                        <option value="Single Supplement">Single Supplement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" class="form-control price-qty" name="price_lines[__INDEX__][quantity]" value="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit Price</label>
                    <input type="number" min="0" step="0.01" class="form-control price-unit" name="price_lines[__INDEX__][unit_price]" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="number" min="0" step="0.01" class="form-control price-total" name="price_lines[__INDEX__][total_price]" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Currency</label>
                    <select class="form-select" name="price_lines[__INDEX__][currency_id]">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}" @selected($currency->id == $version->currency_id)>{{ $currency->short_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Source Type</label>
                    <select class="form-select select2-pricing-tag" name="price_lines[__INDEX__][source_type]" data-placeholder="Pick or type">
                        <option value=""></option>
                        <option value="manual" selected>manual</option>
                        <option value="Budget">Budget</option>
                        <option value="Accommodation">Accommodation</option>
                        <option value="Transport">Transport</option>
                        <option value="Activity">Activity</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="hidden" name="price_lines[__INDEX__][source_id]" value="">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Display Options</label>
                    <div class="d-flex flex-wrap mt-2" style="gap: 18px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="price_lines[__INDEX__][is_optional]" value="1">
                            <label class="form-check-label">Optional</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="price_lines[__INDEX__][is_visible]" value="1" checked>
                            <label class="form-check-label">Visible in quote</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="payment-term-template">
        <div class="builder-repeater-item p-3 mb-2 payment-term-row" data-row-index="__INDEX__">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="payment_terms[__INDEX__][title]" value="" placeholder="Title">
                </div>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="payment_terms[__INDEX__][description]" value="" placeholder="Description">
                </div>
                <div class="col-md-1">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="payment_terms[__INDEX__][is_visible]" value="1" checked>
                    </div>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">x</button>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('script')
    <script>
        (function() {
            // FX_RATES semantics: rate = how many of this currency equals 1 USD (USD-base).
            // To convert from currency X to currency Y: amount * rateY / rateX.
            const FX_RATES = @json($exchangeRates ?? []);
            const BASE_CURRENCY_ID = {{ (int) ($version->currency_id ?? 0) }};
            const BASE_CURRENCY_CODE = @json($currencyCode);

            const fxRateFor = (currencyId) => {
                const id = Number(currencyId);
                if (!id) return 0;
                const entry = FX_RATES[id];
                const rate = entry ? Number(entry.rate) : 0;
                return rate > 0 ? rate : 0;
            };

            const fxCodeFor = (currencyId) => {
                const id = Number(currencyId);
                const entry = id ? FX_RATES[id] : null;
                return entry?.short_name || BASE_CURRENCY_CODE;
            };

            const convertCurrency = (amount, fromId, toId) => {
                if (Number(fromId) === Number(toId)) {
                    return amount;
                }
                const fromRate = fxRateFor(fromId);
                const toRate = fxRateFor(toId);
                if (fromRate <= 0 || toRate <= 0) {
                    return amount;
                }
                return amount * toRate / fromRate;
            };

            const bindRemoveButtons = (root = document) => {
                root.querySelectorAll('.remove-row').forEach(button => {
                    if (button.dataset.bound === '1') {
                        return;
                    }

                    button.dataset.bound = '1';
                    button.addEventListener('click', function() {
                        const row = this.closest('.day-row, .price-line-row, .term-row, .payment-term-row');
                        if (!row) {
                            return;
                        }
                        row.remove();
                        updateSummaries();
                        renumberDays();
                    });
                });
            };

            const appendTemplate = (templateId, containerId, replacements) => {
                const template = document.getElementById(templateId);
                const container = document.getElementById(containerId);
                if (!template || !container) {
                    return;
                }

                const nextIndex = Number(container.dataset.nextIndex || 0);
                const dayNumber = container.children.length + 1;
                let html = template.innerHTML
                    .replaceAll('__INDEX__', nextIndex)
                    .replaceAll('__DAY__', dayNumber);

                Object.entries(replacements || {}).forEach(([key, value]) => {
                    html = html.replaceAll(key, value);
                });

                container.insertAdjacentHTML('beforeend', html);
                container.dataset.nextIndex = nextIndex + 1;
                bindRemoveButtons(container);
                updateSummaries();
                renumberDays();
            };

            const renumberDays = () => {
                document.querySelectorAll('#day-rows .day-row').forEach((row, index) => {
                    const label = row.querySelector('.day-row-number');
                    const input = row.querySelector('.day-number-input');
                    if (label) {
                        label.textContent = index + 1;
                    }
                    if (input && !input.value) {
                        input.value = index + 1;
                    }
                });
                const dayCount = document.querySelectorAll('#day-rows .day-row').length || 1;
                const daySummary = document.getElementById('day-count-summary');
                if (daySummary) {
                    daySummary.textContent = dayCount;
                }
            };

            const recalcPriceLine = row => {
                const qty = Number(row.querySelector('.price-qty')?.value || 0);
                const unit = Number(row.querySelector('.price-unit')?.value || 0);
                const totalInput = row.querySelector('.price-total');
                if (!totalInput) {
                    return;
                }
                totalInput.value = (qty * unit).toFixed(2);
            };

            const updateSummaries = () => {
                const vatToggle = document.getElementById('vat_enabled');
                const vatRate = (vatToggle && !vatToggle.checked) ? 0 : 0.18;
                let visibleCount = 0;

                const rows = Array.from(document.querySelectorAll('#price-line-rows .price-line-row'));

                // Display currency follows the first visible, non-optional line's dropdown.
                // Falls back to the version's base currency when no such line exists.
                let displayCurrencyId = BASE_CURRENCY_ID;
                for (const row of rows) {
                    const optional = row.querySelector('input[name*="[is_optional]"]');
                    const visible = row.querySelector('input[name*="[is_visible]"]');
                    const currencySelect = row.querySelector('select[name*="[currency_id]"]');
                    if (visible?.checked && !optional?.checked && currencySelect?.value) {
                        displayCurrencyId = Number(currencySelect.value);
                        break;
                    }
                }
                const displayCurrencyCode = fxCodeFor(displayCurrencyId);

                let total = 0;
                rows.forEach(row => {
                    const totalInput = row.querySelector('.price-total');
                    const optional = row.querySelector('input[name*="[is_optional]"]');
                    const visible = row.querySelector('input[name*="[is_visible]"]');
                    const currencySelect = row.querySelector('select[name*="[currency_id]"]');

                    if (visible?.checked) {
                        visibleCount++;
                    }

                    if (!optional?.checked) {
                        const lineTotal = Number(totalInput?.value || 0);
                        const lineCurrencyId = currencySelect ? Number(currencySelect.value) : displayCurrencyId;
                        total += convertCurrency(lineTotal, lineCurrencyId, displayCurrencyId);
                    }
                });

                const totalSummary = document.getElementById('quote-total-summary');
                if (totalSummary) {
                    const totalWithVat = total + (total * vatRate);
                    totalSummary.textContent = displayCurrencyCode + ' ' + totalWithVat.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                const visibleSummary = document.getElementById('visible-price-lines-summary');
                if (visibleSummary) {
                    visibleSummary.textContent = visibleCount;
                }
            };

            document.getElementById('add-day-row')?.addEventListener('click', () => {
                appendTemplate('day-row-template', 'day-rows');
            });

            document.getElementById('add-price-line')?.addEventListener('click', () => {
                appendTemplate('price-line-template', 'price-line-rows');
            });

            document.getElementById('add-payment-term-row')?.addEventListener('click', () => {
                appendTemplate('payment-term-template', 'payment-term-rows');
            });

            document.addEventListener('input', function(event) {
                const priceRow = event.target.closest('.price-line-row');
                if (priceRow && (event.target.classList.contains('price-qty') || event.target.classList.contains('price-unit'))) {
                    recalcPriceLine(priceRow);
                    updateSummaries();
                }

                if (event.target.closest('#day-rows') && event.target.classList.contains('day-number-input')) {
                    const label = event.target.closest('.day-row')?.querySelector('.day-row-number');
                    if (label) {
                        label.textContent = event.target.value || '';
                    }
                }
            });

            const refreshDestinationWarning = () => {
                const warning = document.getElementById('destination-warning');
                if (!warning) return;
                const anySelected = Array.from(document.querySelectorAll('#day-rows .library-destination-picker'))
                    .some(sel => !!sel.value);
                warning.classList.toggle('d-none', anySelected);
            };

            document.addEventListener('change', function(event) {
                if (event.target.closest('#price-line-rows')) {
                    updateSummaries();
                }
                if (event.target.id === 'vat_enabled') {
                    updateSummaries();
                }
                if (event.target.classList?.contains('library-destination-picker')
                    || event.target.closest('#day-rows')) {
                    refreshDestinationWarning();
                }
            });

            refreshDestinationWarning();

            const accommodationMatcher = function (params, data) {
                if (!data.id) return data;
                const $j = window.jQuery;
                const term = ((params.term || '') + '').trim().toLowerCase();
                if (term && (data.text || '').toLowerCase().indexOf(term) === -1) return null;

                const optionEl = data.element || null;
                if (!optionEl) return data;
                const row = optionEl.closest('.day-row');
                const destSelect = row ? row.querySelector('.library-destination-picker') : null;
                const currentDestId = destSelect ? (destSelect.value || '') : '';
                if (!currentDestId) return data;

                const allowedIds = (optionEl.getAttribute('data-destination-ids') || '')
                    .split(',').map(s => s.trim()).filter(Boolean);
                if (allowedIds.length === 0) return data;
                return allowedIds.indexOf(currentDestId) === -1 ? null : data;
            };

            const initLibraryPicker = (selectEl) => {
                if (!selectEl || selectEl.dataset.libraryReady === '1') return;
                if (typeof window.jQuery !== 'undefined' && window.jQuery.fn.select2) {
                    window.jQuery(selectEl).select2({
                        width: '100%',
                        placeholder: '— Pick from Library —',
                        allowClear: true,
                        matcher: accommodationMatcher,
                    });
                }
                selectEl.dataset.libraryReady = '1';
            };

            const initDestinationPicker = (selectEl) => {
                if (!selectEl || selectEl.dataset.destReady === '1') return;
                if (typeof window.jQuery !== 'undefined' && window.jQuery.fn.select2) {
                    window.jQuery(selectEl).select2({
                        width: '100%',
                        placeholder: 'Select destination',
                        allowClear: true,
                    });
                    window.jQuery(selectEl).off('change.libdest').on('change.libdest', function () {
                        const row = this.closest('.day-row');
                        if (!row) return;
                        const accomPicker = row.querySelector('.library-accommodation-picker');
                        if (!accomPicker) return;
                        const selectedOpt = accomPicker.options[accomPicker.selectedIndex];
                        if (selectedOpt && this.value) {
                            const allowed = (selectedOpt.getAttribute('data-destination-ids') || '')
                                .split(',').map(s => s.trim()).filter(Boolean);
                            if (allowed.length > 0 && allowed.indexOf(this.value) === -1) {
                                window.jQuery(accomPicker).val('').trigger('change');
                            }
                        }
                    });
                }
                selectEl.dataset.destReady = '1';
            };

            const syncRowFromLibraryPicker = (selectEl) => {
                const row = selectEl.closest('.day-row');
                if (!row) return;
                const id = selectEl.value;
                const opt = selectEl.options[selectEl.selectedIndex];
                const idInput = row.querySelector('.library-accommodation-id');
                const nameInput = row.querySelector('.library-accommodation-name');
                const stayInput = row.querySelector('.library-stay-type');
                if (idInput) idInput.value = id || '';
                if (id && opt) {
                    if (nameInput) {
                        nameInput.placeholder = 'Library uses: ' + (opt.dataset.name || '') + ' — type here to override';
                    }
                    if (stayInput && !stayInput.value) {
                        stayInput.value = opt.dataset.stay || '';
                    }
                } else if (nameInput) {
                    nameInput.placeholder = 'Custom name (override library)';
                }
            };

            const initLibraryPickersIn = (root) => {
                const scope = root || document;
                scope.querySelectorAll('.library-accommodation-picker').forEach(sel => {
                    initLibraryPicker(sel);
                    if (typeof window.jQuery !== 'undefined') {
                        window.jQuery(sel).off('change.libpicker').on('change.libpicker', function () {
                            syncRowFromLibraryPicker(this);
                        });
                    } else {
                        sel.removeEventListener('change', sel._libpickerHandler);
                        sel._libpickerHandler = () => syncRowFromLibraryPicker(sel);
                        sel.addEventListener('change', sel._libpickerHandler);
                    }
                });
                scope.querySelectorAll('.library-destination-picker').forEach(initDestinationPicker);
            };

            // Runs after the original add-day-row handler (registered earlier),
            // so the new row is already in the DOM when this fires.
            document.getElementById('add-day-row')?.addEventListener('click', () => {
                const container = document.getElementById('day-rows');
                if (container) initLibraryPickersIn(container.lastElementChild);
            });

            const initAddonPickers = () => {
                if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) {
                    return;
                }
                document.querySelectorAll('select.select2-addons').forEach(sel => {
                    if (sel.dataset.addonReady === '1') return;
                    window.jQuery(sel).select2({
                        width: '100%',
                        placeholder: sel.dataset.placeholder || 'Select…',
                        allowClear: true,
                    });
                    sel.dataset.addonReady = '1';
                });
            };

            const initActivityPickersIn = (root) => {
                if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) {
                    return;
                }
                const scope = root || document;
                scope.querySelectorAll('select.select2-activities').forEach(sel => {
                    if (sel.dataset.activityReady === '1') return;
                    window.jQuery(sel).select2({
                        width: '100%',
                        placeholder: sel.dataset.placeholder || 'Pick from activities library',
                        allowClear: true,
                    });
                    sel.dataset.activityReady = '1';
                });
            };

            const initPricingTagPickersIn = (root) => {
                if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) {
                    return;
                }
                const scope = root || document;
                scope.querySelectorAll('select.select2-pricing-tag').forEach(sel => {
                    if (sel.dataset.pricingTagReady === '1') return;
                    window.jQuery(sel).select2({
                        width: '100%',
                        placeholder: sel.getAttribute('data-placeholder') || 'Pick or type',
                        allowClear: true,
                        tags: true,
                    });
                    sel.dataset.pricingTagReady = '1';
                });
            };

            // Initialize pickers on the new day-row / price-line after it's appended.
            document.getElementById('add-day-row')?.addEventListener('click', () => {
                const container = document.getElementById('day-rows');
                if (container) initActivityPickersIn(container.lastElementChild);
            });

            document.getElementById('add-price-line')?.addEventListener('click', () => {
                const container = document.getElementById('price-line-rows');
                if (container) initPricingTagPickersIn(container.lastElementChild);
            });

            bindRemoveButtons();
            document.querySelectorAll('#price-line-rows .price-line-row').forEach(recalcPriceLine);
            renumberDays();
            updateSummaries();
            initLibraryPickersIn(document);
            initAddonPickers();
            initActivityPickersIn(document);
            initPricingTagPickersIn(document);
        })();
    </script>
@endsection
