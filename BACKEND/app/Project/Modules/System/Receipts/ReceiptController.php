<?php

namespace App\Project\Modules\System\Receipts;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Receipts\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get receipts
        $receipts = Receipt::orderBy('created_at', 'desc')->get();

        return view('web.system.receipt.index', ['title' => 'Receipts', 'sub_title' => 'All Receipts', 'receipts' => $receipts]);
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
