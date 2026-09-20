<?php

namespace App\Project\Modules\System\Seasons;

use App\Project\Modules\System\Seasons\Services\SaveNewSeasonFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Seasons\Requests\CreateNewSeasonFormRequest;
use App\Project\Modules\System\Seasons\Requests\EditSeasonDetailsFormRequest;
use App\Project\Modules\System\Seasons\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get seasons
        $seasons = Season::orderBy('created_at', 'desc')->get();

        return view('web.system.configuration.season.index', ['title' => 'Seasons', 'sub_title' => 'All Seasons', 'seasons' => $seasons]);
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
    public function store(CreateNewSeasonFormRequest $request, SaveNewSeasonFormAction $saveNewSeasonFormAction)
    {
        //save
        $save = $saveNewSeasonFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'seasons'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'seasons'
        ]);
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
    public function update(EditSeasonDetailsFormRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
