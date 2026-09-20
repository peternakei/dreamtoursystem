<?php

namespace App\Project\Modules\System\Inquiries;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Inquiries\Requests\StaffServiceInquiryRequest;
use App\Project\Modules\System\Inquiries\Services\ServiceInquiryService;
use App\Project\Modules\System\Pages\ContentSupport;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Vehicles\RentalCatalogController;
use App\Project\Modules\System\Vehicles\RentalOffer;
use App\Project\Modules\System\Vehicles\Vehicle;

class StaffServiceInquiryController extends Controller
{
    public function options()
    {
        return ContentSupport::success([
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'currencies' => Currency::orderBy('short_name')->get(['id', 'name', 'short_name']),
            'staff' => User::where('profile', 'SystemUser')->where('is_active', true)->get(['id', 'username']),
            'destinations' => Destination::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'classes' => ServiceClass::orderBy('name')->get(['id', 'name']),
            'trips' => Trip::where('is_published', true)->whereHas('tripStatus', fn ($q) => $q->where('name', 'Approved'))->orderBy('name')->get(['uuid', 'name']),
            'offers' => RentalOffer::where('is_active', true)->where('is_published', true)->orderBy('title')->get(['uuid', 'title', 'purpose', 'vehicle_type', 'vehicle_uuids', 'driver_policy', 'fuel_policy', 'details']),
            'vehicles' => Vehicle::where('is_active', true)->where('is_rental_published', true)->orderBy('name')->get(['uuid', 'name', 'rental_specs']),
            'vehicle_types' => RentalCatalogController::TYPES,
            'timezone' => config('app.timezone'),
        ]);
    }

    public function store(StaffServiceInquiryRequest $request, ServiceInquiryService $service)
    {
        $inquiry = $service->createForStaff($request->validated(), $request->user());

        return ContentSupport::success(['inquiry' => ServiceInquiryService::resource($inquiry, true)], 'Service request created');
    }
}
