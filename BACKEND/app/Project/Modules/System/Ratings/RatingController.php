<?php

namespace App\Project\Modules\System\Ratings;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Ratings\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all ratings
        $ratings = Rating::orderBy('created_at', 'desc')->get();

        return view('web.system.rating.index', ['title' => 'Ratings & Reviews', 'sub_title' => 'All Ratings & Reviews', 'ratings' => $ratings]);
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
        //
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
}
