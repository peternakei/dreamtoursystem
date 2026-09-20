<?php

namespace App\Project\Modules\System\Testimonials\ApiControllers;

use App\Project\Modules\System\Testimonials\Services\Api\GetAllTestimonialsFormAction;
use App\Project\Modules\System\Testimonials\Services\Api\SaveNewTestimonialFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Testimonials\Requests\Api\SaveNewTestimonialsFormRequest;

class TestimonialController extends Controller
{
    public function saveTestimonial(SaveNewTestimonialsFormRequest $request, SaveNewTestimonialFormAction $saveNewTestimonialFormAction)
    {
        return $saveNewTestimonialFormAction->handle($request);
    }

    public function getTestimonials(GetAllTestimonialsFormAction $getAllTestimonialsFormAction)
    {
        return $getAllTestimonialsFormAction->handle();
    }
}
