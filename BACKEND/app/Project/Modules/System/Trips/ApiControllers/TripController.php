<?php

namespace App\Project\Modules\System\Trips\ApiControllers;

use App\Project\Modules\System\Trips\Services\Api\GetTripDetailsFormAction;
use App\Project\Modules\System\Trips\Services\Api\GetTripsFormAction;
use App\Project\Modules\System\Trips\Services\Api\SearchTripFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Trips\Requests\Api\SearchTripFormRequest;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function getTrips(Request $request, GetTripsFormAction $getTripsFormAction)
    {
        return $getTripsFormAction->handle($request);
    }

    public function getTripDetails(Request $request, GetTripDetailsFormAction $getTripDetailsFormAction, $id)
    {
        return $getTripDetailsFormAction->handle($request, $id);
    }

    public function searchTrip(SearchTripFormRequest $request, SearchTripFormAction $searchTripFormAction)
    {
        return $searchTripFormAction->handle($request);
    }
}
