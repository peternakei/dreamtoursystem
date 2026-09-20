<?php

namespace App\Project\Modules\System\Ratings\ApiControllers;

use App\Project\Modules\System\Ratings\Services\Api\SubmitRatingAction;
use App\Project\Modules\System\Ratings\Services\Api\UpdateRatingAction;
use App\Project\Modules\System\Ratings\Services\Api\DeleteRatingAction;
use App\Project\Modules\System\Ratings\Services\Api\GetUserRatingsAction;
use App\Project\Modules\System\Ratings\Services\Api\CheckRatingStatusAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Ratings\Requests\Api\SubmitRatingRequest;
use App\Project\Modules\System\Ratings\Requests\Api\UpdateRatingRequest;
use Illuminate\Http\Request;

class RateAndReviewController extends Controller
{
    public function submitRating(SubmitRatingRequest $request, SubmitRatingAction $submitRatingAction)
    {
        return $submitRatingAction->handle($request);
    }

    public function updateRating($ratingUuid, UpdateRatingRequest $request, UpdateRatingAction $updateRatingAction)
    {
        return $updateRatingAction->handle($request, $ratingUuid);
    }

    public function deleteRating($ratingUuid, Request $request, DeleteRatingAction $deleteRatingAction)
    {
        return $deleteRatingAction->handle($ratingUuid, $request);
    }

    public function getUserRatings(Request $request, GetUserRatingsAction $getUserRatingsAction)
    {
        return $getUserRatingsAction->handle($request);
    }

    public function checkRatingStatus(Request $request, $type, CheckRatingStatusAction $checkRatingStatusAction)
    {
        return $checkRatingStatusAction->handle($request, $type);
    }
}
