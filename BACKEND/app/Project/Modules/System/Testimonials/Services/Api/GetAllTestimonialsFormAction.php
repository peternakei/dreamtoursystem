<?php

namespace App\Project\Modules\System\Testimonials\Services\Api;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\System\Testimonials\Testimonial;
use Illuminate\Http\Request;

class GetAllTestimonialsFormAction
{
    public function handle()
    {
        $testimonials = Testimonial::with(['tourist' => function ($query) {
            $query->select('id', 'name','phone','email','tourist_number','country_id');
        }])->where('is_approved',true)->get()->map(function($test){
            return [
                'id' => $test->id,
                'uuid' => $test->uuid,
                'comments' => $test->comments,
                'tourist' => $test->tourist ? [
                    'id' => $test->tourist->id,
                    'name' => $test->tourist->name,
                    'phone' => $test->tourist->phone,
                    'email' => $test->tourist->email,
                    'tourist_number' => $test->tourist->tourist_number,
                    'country' => Country::where('id', $test->tourist->country_id)->value('name') ?? '',
                    'passport' => asset('storage/uploads/' . $test->tourist->passport()->value('name')) ?? '',
                ] : null,
            ];
        });

        return response()->json([
            'status' => "success",
            'code' => 200,
            'message' => 'Testimonials fetched successfully',
            'data' => [
                'testimonials' => $testimonials
            ]
        ]);
    }
}
