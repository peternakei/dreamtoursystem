<?php

namespace App\Project\Modules\System\Splash\ApiControllers;

use App\Project\Modules\System\Splash\Services\Api\GetSplashFormAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SplashController extends Controller
{
    public function getSplash(GetSplashFormAction $getSplashFormAction)
    {
        return $getSplashFormAction->handle();
    }
}
