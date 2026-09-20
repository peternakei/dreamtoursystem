<?php

namespace App\Project\Modules\Core\Users\ApiControllers;

use App\Project\Modules\Core\Users\Services\Api\CheckUserInteractionStatusAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserInteractionController extends Controller
{
    public function checkInteractionStatus(Request $request, CheckUserInteractionStatusAction $checkUserInteractionStatusAction)
    {
        return $checkUserInteractionStatusAction->handle($request);
    }
}
