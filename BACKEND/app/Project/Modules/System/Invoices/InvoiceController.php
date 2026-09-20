<?php

namespace App\Project\Modules\System\Invoices;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Banks\Bank;
use App\Project\Modules\System\BankDetails\BankDetail;
use App\Project\Modules\Core\PaymentModes\PaymentMode;
use App\Project\Modules\System\Invoices\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get invoices
        $invoices = Invoice::orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'All Invoices', 'invoices' => $invoices]);
    }

    public function fullyPaid()
    {
        //get invoices
        $invoices = Invoice::where('invoice_status_id', 1)->orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'Fully Paid Invoices', 'invoices' => $invoices]);
    }

    public function partialPaid()
    {
        //get invoices
        $invoices = Invoice::where('invoice_status_id', 2)->orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'Partial Paid Invoices', 'invoices' => $invoices]);
    }

    public function pending()
    {
        //get invoices
        $invoices = Invoice::where('invoice_status_id', 3)->orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'Pending Invoices', 'invoices' => $invoices]);
    }

    public function cancelled()
    {
        //get invoices
        $invoices = Invoice::where('invoice_status_id', 4)->orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'Cancelled Invoices', 'invoices' => $invoices]);
    }

    public function expired()
    {
        //get invoices
        $invoices = Invoice::where('invoice_status_id', 5)->orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'Expired Invoices', 'invoices' => $invoices]);
    }

    public function refunded()
    {
        //get invoices
        $invoices = Invoice::where('invoice_status_id', 6)->orderBy('created_at', 'desc')->get();

        return view('web.system.invoice.index', ['title' => 'Invoices', 'sub_title' => 'Refunded Invoices', 'invoices' => $invoices]);
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
        //get invoice
        $invoice = Invoice::where('uuid', $id)->first();
        $paymentModes = PaymentMode::all();
        $banks = BankDetail::all();

        return view('web.system.invoice.show', ['invoice' => $invoice]);
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
