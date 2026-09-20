<?php

namespace App\Project\Modules\System\Trips;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\Services\ItineraryService;
use Illuminate\Http\Request;

class TripPlannerController extends Controller
{
    public function edit(ItineraryService $itineraryService, string $id)
    {
        $trip = Trip::with(['tripDays.activities', 'tripDays.destination.images', 'destinations.destination'])
            ->where('uuid', $id)
            ->firstOrFail();

        $seedRows = $trip->tripDays->count() > 0
            ? $trip->tripDays->map(function ($day) {
                return [
                    'day_number' => $day->day_number,
                    'title' => $day->title,
                    'destination_id' => $day->destination_id,
                    'accommodation_name' => $day->accommodation_name,
                    'accommodation_notes' => $day->accommodation_notes,
                    'stay_type' => $day->stay_type,
                    'nights' => $day->nights,
                    'breakfast' => $day->breakfast,
                    'lunch' => $day->lunch,
                    'dinner' => $day->dinner,
                    'description' => $day->description,
                    'activity_lines' => $day->activities->pluck('title')->filter()->implode("\n"),
                ];
            })->all()
            : $itineraryService->seedTripDayPayloads($trip);

        return view('web.system.trip.planner', [
            'trip' => $trip,
            'seedRows' => $seedRows,
            'destinations' => Destination::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ItineraryService $itineraryService, string $id)
    {
        $validated = $request->validate([
            'days' => 'nullable|array',
        ]);

        $trip = Trip::where('uuid', $id)->firstOrFail();
        $itineraryService->syncTripDays($trip, $request->input('days', []), auth('web')->id());

        return redirect()
            ->route('trips.planner.edit', $id)
            ->with('success', 'Trip itinerary saved successfully.');
    }
}
