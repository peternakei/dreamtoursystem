<?php

namespace App\Project\Modules\Core\Regions;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Regions\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get regions
        $regions = Region::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.region.index', ['title' => 'Regions', 'sub_title' => 'All Regions', 'regions' => $regions]);
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
