@extends('layouts.app')

@section('content')
    @php
        $tripFromDate = $trip->from_date ? \Carbon\Carbon::parse($trip->from_date)->format('M d, Y') : 'TBD';
        $tripToDate = $trip->to_date ? \Carbon\Carbon::parse($trip->to_date)->format('M d, Y') : 'TBD';
    @endphp
    <style>
        .trip-planner-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(15, 93, 75, 0.96), rgba(23, 52, 45, 0.9));
            color: #fff;
        }

        .trip-planner-card {
            border-radius: 18px;
            border: 1px solid rgba(15, 93, 75, 0.08);
            box-shadow: 0 10px 24px rgba(23, 52, 45, 0.06);
        }

        .trip-planner-row {
            border: 1px solid #e7ece9;
            border-radius: 16px;
            background: #fcfdfd;
        }
    </style>

    <div class="row mt-3">
        <div class="col-12">
            <div class="back-button">
                <a href="{{ route('trips.show', $trip->uuid) }}" class="text-muted">
                    <span style="font-size: 1.5em;"><i class="uil uil-arrow-circle-left"></i></span>
                </a>
            </div>
            <div class="trip-planner-hero p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                    <div>
                        <span class="badge bg-light text-dark mb-2">{{ $trip->trip_code }}</span>
                        <h2 class="mb-2" style="font-size: 2rem;">{{ $trip->name }}</h2>
                        <p class="mb-0" style="opacity: 0.9;">Build the reusable itinerary library for this trip so quotations can be generated with day structure, descriptions, stay notes, and activities already in place.</p>
                    </div>
                    <div class="text-lg-end">
                        <div class="badge bg-dark-subtle text-dark mb-2">{{ $trip->tripType?->name ?? 'Trip' }}</div>
                        <div>{{ $tripFromDate }} to {{ $tripToDate }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('trips.planner.update', $trip->uuid) }}" method="POST" id="trip-planner-form">
        @csrf
        @method('PUT')

        <div class="row mt-4">
            <div class="col-lg-9">
                <div class="card trip-planner-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <div>
                                <h5 class="mb-1">Reusable Trip Itinerary</h5>
                                <p class="text-muted mb-0">This becomes the default structure when staff create a quote from the trip.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-trip-day-row">
                                <i class="uil uil-plus"></i> Add Day
                            </button>
                        </div>

                        <div id="trip-day-rows" data-next-index="{{ count($seedRows) }}">
                            @foreach ($seedRows as $index => $row)
                                <div class="trip-planner-row p-3 mb-3 trip-day-row" data-row-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Day <span class="trip-day-number">{{ $row['day_number'] ?? ($index + 1) }}</span></h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-trip-row">Remove</button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label">Day No.</label>
                                            <input type="number" min="1" class="form-control trip-day-number-input" name="days[{{ $index }}][day_number]" value="{{ $row['day_number'] ?? ($index + 1) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="days[{{ $index }}][title]" value="{{ $row['title'] ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Destination</label>
                                            <select class="form-select" name="days[{{ $index }}][destination_id]">
                                                <option value="">Select destination</option>
                                                @foreach ($destinations as $destination)
                                                    <option value="{{ $destination->id }}" @selected(($row['destination_id'] ?? null) == $destination->id)>{{ $destination->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Accommodation</label>
                                            <input type="text" class="form-control" name="days[{{ $index }}][accommodation_name]" value="{{ $row['accommodation_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Stay Type</label>
                                            <input type="text" class="form-control" name="days[{{ $index }}][stay_type]" value="{{ $row['stay_type'] ?? '' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Nights</label>
                                            <input type="number" min="0" class="form-control" name="days[{{ $index }}][nights]" value="{{ $row['nights'] ?? 0 }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Meals</label>
                                            <div class="d-flex flex-wrap mt-2" style="gap: 12px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[{{ $index }}][breakfast]" value="1" @checked($row['breakfast'] ?? false)>
                                                    <label class="form-check-label">Breakfast</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[{{ $index }}][lunch]" value="1" @checked($row['lunch'] ?? false)>
                                                    <label class="form-check-label">Lunch</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="days[{{ $index }}][dinner]" value="1" @checked($row['dinner'] ?? false)>
                                                    <label class="form-check-label">Dinner</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Accommodation Notes</label>
                                            <textarea class="form-control" rows="3" name="days[{{ $index }}][accommodation_notes]">{{ $row['accommodation_notes'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" rows="3" name="days[{{ $index }}][description]">{{ $row['description'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Activities</label>
                                            <textarea class="form-control" rows="3" name="days[{{ $index }}][activity_lines]" placeholder="One activity per line">{{ $row['activity_lines'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card trip-planner-card">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Trip Planner Notes</h5>
                        <p class="text-muted">Once saved, these days become the default quotation draft for this trip. Destination images, points, and addons will still be pulled automatically for the quote preview.</p>

                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div class="text-muted small">Current duration</div>
                            <div class="fw-semibold" id="trip-day-count-summary">{{ count($seedRows) }}</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Save Trip Planner</button>
                            <a href="{{ route('trips.show', $trip->uuid) }}" class="btn btn-outline-secondary">Back to Trip</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <template id="trip-day-template">
        <div class="trip-planner-row p-3 mb-3 trip-day-row" data-row-index="__INDEX__">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Day <span class="trip-day-number">__DAY__</span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-trip-row">Remove</button>
            </div>
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Day No.</label>
                    <input type="number" min="1" class="form-control trip-day-number-input" name="days[__INDEX__][day_number]" value="__DAY__">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="days[__INDEX__][title]" value="Day __DAY__">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Destination</label>
                    <select class="form-select" name="days[__INDEX__][destination_id]">
                        <option value="">Select destination</option>
                        @foreach ($destinations as $destination)
                            <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Accommodation</label>
                    <input type="text" class="form-control" name="days[__INDEX__][accommodation_name]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stay Type</label>
                    <input type="text" class="form-control" name="days[__INDEX__][stay_type]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nights</label>
                    <input type="number" min="0" class="form-control" name="days[__INDEX__][nights]" value="0">
                </div>
                <div class="col-md-4">
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
                    <textarea class="form-control" rows="3" name="days[__INDEX__][activity_lines]" placeholder="One activity per line"></textarea>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('script')
    <script>
        (function() {
            const container = document.getElementById('trip-day-rows');
            const template = document.getElementById('trip-day-template');

            const bindButtons = () => {
                document.querySelectorAll('.remove-trip-row').forEach(button => {
                    if (button.dataset.bound === '1') {
                        return;
                    }
                    button.dataset.bound = '1';
                    button.addEventListener('click', function() {
                        this.closest('.trip-day-row')?.remove();
                        updateSummary();
                    });
                });
            };

            const updateSummary = () => {
                document.querySelectorAll('.trip-day-row').forEach((row, index) => {
                    const label = row.querySelector('.trip-day-number');
                    if (label) {
                        label.textContent = index + 1;
                    }
                });
                document.getElementById('trip-day-count-summary').textContent = document.querySelectorAll('.trip-day-row').length;
            };

            document.getElementById('add-trip-day-row')?.addEventListener('click', function() {
                const nextIndex = Number(container.dataset.nextIndex || 0);
                const dayNumber = document.querySelectorAll('.trip-day-row').length + 1;
                const html = template.innerHTML
                    .replaceAll('__INDEX__', nextIndex)
                    .replaceAll('__DAY__', dayNumber);
                container.insertAdjacentHTML('beforeend', html);
                container.dataset.nextIndex = nextIndex + 1;
                bindButtons();
                updateSummary();
            });

            bindButtons();
            updateSummary();
        })();
    </script>
@endsection
