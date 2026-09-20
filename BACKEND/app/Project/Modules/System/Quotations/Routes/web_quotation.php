<?php

use App\Project\Modules\System\Quotations\QuotationController;
use App\Project\Modules\System\Quotations\QuotationPdfController;
use App\Project\Modules\System\Quotations\QuotationVersionController;
use App\Project\Modules\System\Quotations\PublicItineraryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('inquiries/{id}/quotations/create', [QuotationController::class, 'createFromInquiry'])
        ->name('quotations.create_from_inquiry');
    Route::post('inquiries/{id}/quotations', [QuotationController::class, 'storeFromInquiry'])
        ->name('quotations.store_from_inquiry');

    Route::get('quotation-versions/{id}/builder', [QuotationVersionController::class, 'edit'])
        ->name('quotation_versions.edit');
    Route::put('quotation-versions/{id}/builder', [QuotationVersionController::class, 'update'])
        ->name('quotation_versions.update');
    Route::get('quotation-versions/{id}/preview', [QuotationVersionController::class, 'preview'])
        ->name('quotation_versions.preview');
    Route::post('quotation-versions/{id}/generate-pdf', [QuotationPdfController::class, 'generate'])
        ->name('quotation_versions.pdf.generate');
    Route::get('quotation-versions/{id}/download-pdf', [QuotationPdfController::class, 'download'])
        ->name('quotation_versions.pdf.download');
    Route::get('quotation-versions/{id}/print-preview', [QuotationPdfController::class, 'printPreview'])
        ->name('quotation_versions.pdf.print_preview');
    Route::post('quotation-versions/{id}/share-link', [QuotationPdfController::class, 'shareLink'])
        ->name('quotation_versions.share_link');
    Route::post('quotation-versions/{id}/book', [QuotationVersionController::class, 'book'])
        ->name('quotation_versions.book');

    Route::prefix('quotations')->controller(QuotationController::class)->group(function () {
        Route::get('won', 'won')->name('quotations.won');
        Route::get('open', 'open')->name('quotations.open');
        Route::get('lost', 'lost')->name('quotations.lost');
        Route::post('change_status/{id}', 'changeStatus')->name('quotations.change_status');
        Route::post('{id}/duplicate-version', 'duplicateVersion')->name('quotations.duplicate_version');
    });

    Route::resources([
        'quotations' => QuotationController::class
    ]);
});

Route::get('itinerary/{token}/{slug?}', [PublicItineraryController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('public.itinerary.show');
Route::post('itinerary/{token}/accept', [PublicItineraryController::class, 'accept'])
    ->name('public.itinerary.accept');
Route::post('itinerary/{token}/reject', [PublicItineraryController::class, 'reject'])
    ->name('public.itinerary.reject');
Route::post('itinerary/{token}/request-changes', [PublicItineraryController::class, 'requestChanges'])
    ->name('public.itinerary.request_changes');
Route::get('itinerary/{token}/book', [PublicItineraryController::class, 'confirm'])
    ->name('public.itinerary.book.confirm');
Route::post('itinerary/{token}/book', [PublicItineraryController::class, 'book'])
    ->name('public.itinerary.book');
Route::get('itinerary/{token}/booked', [PublicItineraryController::class, 'thankYou'])
    ->name('public.itinerary.book.thank_you');
Route::get('itinerary/{token}/download-pdf', [PublicItineraryController::class, 'download'])
    ->name('public.itinerary.download');
