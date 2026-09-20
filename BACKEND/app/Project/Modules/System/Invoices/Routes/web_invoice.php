<?php

use App\Project\Modules\System\Invoices\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('invoices')->controller(InvoiceController::class)->group(function () {
        Route::get('fully_paid', 'fullyPaid')->name('invoices.fully_paid');
        Route::get('partial_paid', 'partialPaid')->name('invoices.partial_paid');
        Route::get('pending', 'pending')->name('invoices.pending');
        Route::get('cancelled', 'cancelled')->name('invoices.cancelled');
        Route::get('expired', 'expired')->name('invoices.expired');
        Route::get('refunded', 'refunded')->name('invoices.refunded');
        Route::post('change_status/{id}', 'changeStatus')->name('invoices.change_status');
    });

    Route::resources([
        'invoices' => InvoiceController::class
    ]);
});
