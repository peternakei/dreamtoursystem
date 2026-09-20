<?php

namespace App\Project\Modules\System\Likes\ApiControllers;

use App\Project\Modules\System\Likes\Services\Api\ToggleLikeAction;
use App\Project\Modules\System\Likes\Services\Api\GetUserLikesAction;
use App\Project\Modules\System\Likes\Services\Api\CheckLikeStatusAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Likes\Requests\Api\ToggleLikeRequest;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggleLike(ToggleLikeRequest $request, ToggleLikeAction $toggleLikeAction)
    {
        return $toggleLikeAction->handle($request);
    }

    public function getUserLikes(Request $request, GetUserLikesAction $getUserLikesAction)
    {
        return $getUserLikesAction->handle($request);
    }

    public function checkLikeStatus(Request $request, $type, CheckLikeStatusAction $checkLikeStatusAction)
    {
        return $checkLikeStatusAction->handle($request, $type);
    }
}
