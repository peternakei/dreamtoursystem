<?php

namespace App\Project\Modules\System\Testimonials;

use App\Project\Modules\System\Testimonials\Services\ChangeTestimonialStatusFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Testimonials\Requests\ChangeTestimonialStatusFormRequest;
use App\Project\Modules\System\Testimonials\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get testimonials
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();

        return view('web.system.testimonial.index', ['title' => 'Testimonials', 'sub_title' => 'All Testimonials', 'testimonials' => $testimonials]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get testimonial
        $testimonial = Testimonial::where('uuid', $id)->first();

        return view('web.system.testimonial.show', ['testimonial' => $testimonial]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus(ChangeTestimonialStatusFormRequest $request, ChangeTestimonialStatusFormAction $changeTestimonialStatusFormAction, string $id)
    {
        //change
        $change = $changeTestimonialStatusFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message'],
                'redirect' => 'testimonials/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $change['message'],
            'redirect' => 'testimonials/' . $id
        ]);
    }
}
