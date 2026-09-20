<?php

namespace App\Project\Modules\System\Inquiries\ApiControllers;

use App\Project\Modules\System\Inquiries\Services\Api\GetAllInquiryFormAction;
use App\Project\Modules\System\Inquiries\Services\Api\GetInquiryDetailsFormAction;
use App\Project\Modules\System\Inquiries\Services\Api\SaveNewInquiryFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Inquiries\Requests\Api\CreateNewInquiryFormRequest;
use App\Project\Modules\System\Inquiries\Requests\Api\GetInquiryDetailsFormRequest;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function saveInquiry(CreateNewInquiryFormRequest $request, SaveNewInquiryFormAction $saveNewInquiryFormAction)
    {
        return $saveNewInquiryFormAction->handle($request);
    }

    public function getInquiries(Request $request, GetAllInquiryFormAction $getAllInquiryFormAction)
    {
        return $getAllInquiryFormAction->handle($request);
    }

    public function getInquiryDetails(GetInquiryDetailsFormRequest $request, GetInquiryDetailsFormAction $getInquiryDetailsFormAction)
    {
        return $getInquiryDetailsFormAction->handle($request);
    }
}
