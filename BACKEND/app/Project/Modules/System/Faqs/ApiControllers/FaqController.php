<?php

namespace App\Project\Modules\System\Faqs\ApiControllers;

use App\Project\Modules\System\Faqs\Services\Api\GetAllFaqFormAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function getFaqs(GetAllFaqFormAction $getAllFaqFormAction)
    {
        return $getAllFaqFormAction->handle();
    }
}
