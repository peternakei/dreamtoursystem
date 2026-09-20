<?php

namespace App\Project\Modules\Core\Districts;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Districts\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       //get districts
        $districts = District::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.district.index', ['title' => 'Districts', 'sub_title' => 'All Districts', 'districts' => $districts]);
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
