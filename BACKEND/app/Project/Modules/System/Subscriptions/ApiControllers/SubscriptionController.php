<?php

namespace App\Project\Modules\System\Subscriptions\ApiControllers;

use App\Project\Modules\System\Subscriptions\Services\Api\SaveNewSubscriptionFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Subscriptions\Requests\Api\SaveSubscriptionFormRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function saveSubscription(SaveSubscriptionFormRequest $request, SaveNewSubscriptionFormAction $saveNewSubscriptionFormAction)
    {
        return $saveNewSubscriptionFormAction->handle($request);
    }
}
