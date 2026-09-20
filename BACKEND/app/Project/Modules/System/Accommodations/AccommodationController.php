<?php

namespace App\Project\Modules\System\Accommodations;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Accommodations\Requests\CreateNewAccommodationFormRequest;
use App\Project\Modules\System\Accommodations\Requests\EditAccommodationDetailsFormRequest;
use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Accommodations\StayType;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccommodationController extends Controller
{
    public function index()
    {
        return $this->renderList(
            Accommodation::with(['stayType', 'primaryDestination'])
                ->orderBy('sort_order')->orderBy('name')->get(),
            'All Accommodations'
        );
    }

    public function active()
    {
        return $this->renderList(
            Accommodation::with(['stayType', 'primaryDestination'])
                ->where('is_active', true)
                ->orderBy('sort_order')->orderBy('name')->get(),
            'Active Accommodations'
        );
    }

    public function inactive()
    {
        return $this->renderList(
            Accommodation::with(['stayType', 'primaryDestination'])
                ->where('is_active', false)
                ->orderBy('sort_order')->orderBy('name')->get(),
            'Inactive Accommodations'
        );
    }

    protected function renderList($accommodations, string $subTitle)
    {
        return view('web.system.accommodation.index', [
            'title' => 'Accommodations',
            'sub_title' => $subTitle,
            'accommodations' => $accommodations,
            'stay_types' => StayType::where('is_active', true)->orderBy('sort_order')->get(),
            'destinations' => Destination::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(CreateNewAccommodationFormRequest $request)
    {
        DB::transaction(function () use ($request) {
            $accommodation = Accommodation::create([
                'name' => $request->name,
                'stay_type_id' => $request->stay_type_id,
                'primary_destination_id' => $request->primary_destination_id,
                'description' => $request->description,
                'location_text' => $request->location_text,
                'created_by' => Auth::id(),
            ]);

            $this->syncDestinations($accommodation, $request);
        });

        return redirect()->route('accommodations.index')->with('success', 'Accommodation created');
    }

    public function show(string $id)
    {
        $accommodation = Accommodation::with(['stayType', 'primaryDestination', 'destinations'])
            ->where('uuid', $id)
            ->firstOrFail();

        return view('web.system.accommodation.show', [
            'title' => 'Accommodation Profile',
            'sub_title' => $accommodation->name,
            'accommodation' => $accommodation,
            'stay_types' => StayType::where('is_active', true)->orderBy('sort_order')->get(),
            'destinations' => Destination::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(EditAccommodationDetailsFormRequest $request, string $id)
    {
        DB::transaction(function () use ($request, $id) {
            $accommodation = Accommodation::where('uuid', $id)->firstOrFail();
            $accommodation->update([
                'name' => $request->name,
                'stay_type_id' => $request->stay_type_id,
                'primary_destination_id' => $request->primary_destination_id,
                'description' => $request->description,
                'location_text' => $request->location_text,
                'updated_by' => Auth::id(),
            ]);

            $this->syncDestinations($accommodation, $request);
        });

        return redirect()->back()->with('success', 'Accommodation updated');
    }

    public function changeStatus(Request $request, string $id)
    {
        $accommodation = Accommodation::where('uuid', $id)->firstOrFail();
        $accommodation->update([
            'is_active' => !$accommodation->is_active,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Accommodation status changed');
    }

    public function destroy(string $id)
    {
        $accommodation = Accommodation::where('uuid', $id)->firstOrFail();
        $accommodation->update(['updated_by' => Auth::id()]);
        $accommodation->delete();

        return redirect()->route('accommodations.index')->with('success', 'Accommodation deleted');
    }

    protected function syncDestinations(Accommodation $accommodation, Request $request): void
    {
        $destinationIds = collect($request->input('destination_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique();

        if ($request->primary_destination_id) {
            $destinationIds = $destinationIds->push((int) $request->primary_destination_id)->unique();
        }

        $sync = $destinationIds->mapWithKeys(function (int $id, int $idx) use ($request) {
            return [
                $id => [
                    'is_primary' => $request->primary_destination_id == $id,
                    'sort_order' => $idx,
                ],
            ];
        })->all();

        $accommodation->destinations()->sync($sync);
    }
}
