<?php

namespace App\Project\Modules\System\Categories\ApiControllers;

use App\Project\Modules\System\Categories\Services\Api\GetCategoriesFormAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories(Request $request, GetCategoriesFormAction $getCategoriesFormAction)
    {
        return $getCategoriesFormAction->handle($request);
    }
}
