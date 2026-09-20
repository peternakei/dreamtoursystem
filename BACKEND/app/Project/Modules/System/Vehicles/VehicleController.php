<?php

namespace App\Project\Modules\System\Vehicles;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Vehicles\Requests\CreateNewVehicleFormRequest;
use App\Project\Modules\System\Vehicles\Requests\EditVehicleDetailsFormRequest;
use App\Project\Modules\System\Vehicles\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('sort_order')->orderBy('name')->get();

        return view('web.system.vehicle.index', [
            'title' => 'Vehicles',
            'sub_title' => 'All Vehicles',
            'vehicles' => $vehicles,
        ]);
    }

    public function active()
    {
        $vehicles = Vehicle::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('web.system.vehicle.index', [
            'title' => 'Vehicles',
            'sub_title' => 'Active Vehicles',
            'vehicles' => $vehicles,
        ]);
    }

    public function inactive()
    {
        $vehicles = Vehicle::where('is_active', false)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('web.system.vehicle.index', [
            'title' => 'Vehicles',
            'sub_title' => 'Inactive Vehicles',
            'vehicles' => $vehicles,
        ]);
    }

    public function store(CreateNewVehicleFormRequest $request)
    {
        Vehicle::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created');
    }

    public function show(string $id)
    {
        $vehicle = Vehicle::where('uuid', $id)->firstOrFail();

        return view('web.system.vehicle.show', [
            'title' => 'Vehicle Profile',
            'sub_title' => $vehicle->name,
            'vehicle' => $vehicle,
        ]);
    }

    public function update(EditVehicleDetailsFormRequest $request, string $id)
    {
        $vehicle = Vehicle::where('uuid', $id)->firstOrFail();
        $vehicle->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Vehicle updated');
    }

    public function changeStatus(Request $request, string $id)
    {
        $vehicle = Vehicle::where('uuid', $id)->firstOrFail();
        $vehicle->update([
            'is_active' => !$vehicle->is_active,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Vehicle status changed');
    }

    public function destroy(string $id)
    {
        $vehicle = Vehicle::where('uuid', $id)->firstOrFail();
        $vehicle->update(['updated_by' => Auth::id()]);
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted');
    }
}
