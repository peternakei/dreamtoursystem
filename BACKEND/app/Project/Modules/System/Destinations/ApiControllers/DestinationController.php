<?php

namespace App\Project\Modules\System\Destinations\ApiControllers;

use App\Project\Modules\System\Destinations\Services\Api\GetDestinationDetailsFormAction;
use App\Project\Modules\System\Destinations\Services\Api\GetDestinationFormAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function getDestination(Request $request, GetDestinationFormAction $getDestinationFormAction)
    {
        return $getDestinationFormAction->handle($request);
    }

    public function getDestinationDetails(Request $request, GetDestinationDetailsFormAction $getDestinationDetailsFormAction, $id)
    {
        return $getDestinationDetailsFormAction->handle($request, $id);
    }
}
