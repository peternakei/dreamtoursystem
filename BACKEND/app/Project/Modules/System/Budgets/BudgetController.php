<?php

namespace App\Project\Modules\System\Budgets;

use App\Project\Modules\System\Budgets\Services\SaveNewBudgetFormAction;
use App\Project\Modules\System\Budgets\Services\UpdateBudgetDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Budgets\Requests\CreateNewBudgetFormRequest;
use App\Project\Modules\System\Budgets\Requests\EditBudgetDetailsFormRequest;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Seasons\Budget;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get budgets
        $budgets = Budget::orderBy('created_at', 'desc')->get();
        $seasons = Season::all();
        $classes = ServiceClass::all();
        $currencies = Currency::all();
        $trips = Trip::all();

        return view('web.system.configuration.budget.index', ['title' => 'Budgets', 'sub_title' => 'All Budgets', 'classes' => $classes, 'seasons' => $seasons, 'budgets' => $budgets, 'currencies' => $currencies, 'trips' => $trips]);
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
    public function store(CreateNewBudgetFormRequest $request, SaveNewBudgetFormAction $saveNewBudgetFormAction)
    {
        //save
        $save = $saveNewBudgetFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'budgets'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'budgets'
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
        //get budget
        $budget = Budget::where('uuid', $id)->first();
        $seasons = Season::all();
        $classes = ServiceClass::all();
        $currencies = Currency::all();
        $trips = Trip::all();

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $budget,
            'seasons' => $seasons,
            'currencies' => $currencies,
            'classes' => $classes,
            'trips' => $trips,
            'season_name' => $budget->season->name,
            'html' => '<input type="hidden" name="budget_id" id="budget_id" value="' . $budget->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditBudgetDetailsFormRequest $request, UpdateBudgetDetailsFormAction $updateBudgetDetailsFormAction, string $id)
    {
        //update
        $update = $updateBudgetDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'budgets'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Budget updated successfully.',
            'redirect' => 'budgets'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Budget::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete budget details',
                'redirect' => 'budgets'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Budget deleted successfully',
            'redirect' => 'budgets'
        ]);
    }
}
