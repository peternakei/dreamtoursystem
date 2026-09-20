<?php

use App\Project\Modules\System\Inquiries\ServiceAccess;
use App\Project\Modules\System\Inquiries\ServiceOperationsController;
use App\Project\Modules\System\Inquiries\StaffServiceInquiryController;
use App\Project\Modules\System\Pages\PageContentController;
use App\Project\Modules\System\Vehicles\RentalCatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('workspace')->name('workspace.')->middleware('auth:web')->group(function () {
    Route::middleware(ServiceAccess::class.':manage_rental_offers')->controller(RentalCatalogController::class)->group(function () {
        Route::get('rental-offers', 'adminIndex')->name('rental-offers.index');
        Route::post('rental-offers', 'save')->name('rental-offers.store');
        Route::put('rental-offers/{uuid}', 'save')->whereUuid('uuid')->name('rental-offers.update');
        Route::put('vehicles/{uuid}/rental', 'saveVehicle')->whereUuid('uuid')->name('vehicles.rental');
    });
    Route::middleware(ServiceAccess::class.':manage_service_inquiries')->controller(ServiceOperationsController::class)->group(function () {
        Route::get('service-inquiries/options', [StaffServiceInquiryController::class, 'options'])->name('service-inquiries.options');
        Route::post('service-inquiries', [StaffServiceInquiryController::class, 'store'])->name('service-inquiries.store');
        Route::get('service-inquiries', 'index')->name('service-inquiries.index');
        Route::get('service-inquiries/{uuid}', 'show')->whereUuid('uuid')->name('service-inquiries.show');
        Route::put('service-inquiries/{uuid}', 'update')->whereUuid('uuid')->name('service-inquiries.update');
        Route::post('service-inquiries/{uuid}/quotations', 'quote')->whereUuid('uuid')->name('service-inquiries.quote');
        Route::post('service-inquiries/{uuid}/book', 'book')->whereUuid('uuid')->name('service-inquiries.book');
    });
    Route::middleware(ServiceAccess::class.':manage_page_content')->controller(PageContentController::class)->group(function () {
        Route::get('pages-content', 'index')->name('pages-content.index');
        Route::post('pages-content', 'save')->name('pages-content.store');
        Route::put('pages-content/{uuid}', 'save')->whereUuid('uuid')->name('pages-content.update');
        Route::get('travel-content', 'travelOptions')->name('travel-content.index');
        Route::put('travel-content/{type}/{uuid}', 'saveTravel')->whereUuid('uuid')->name('travel-content.update');
    });
});
