<?php

namespace App\Project\Modules\System\Dashboard\ApiControllers;

use App\Project\Modules\System\Dashboard\Services\Api\GetUserDashboardAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getUserDashboard(Request $request, GetUserDashboardAction $getUserDashboardAction)
    {
        return $getUserDashboardAction->handle($request);
    }
}
