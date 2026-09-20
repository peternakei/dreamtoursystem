<?php

namespace App\Providers;

use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Invoices\Invoice;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Bookings\Observers\BookingObserver;
use App\Project\Modules\System\Inquiries\Observers\InquiryObserver;
use App\Project\Modules\System\Invoices\Observers\InvoiceObserver;
use App\Project\Modules\System\Tourists\Observers\TouristObserver;
use App\Project\Modules\System\Trips\Observers\TripObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();

        Tourist::observe(TouristObserver::class);
        Trip::observe(TripObserver::class);
        Booking::observe(BookingObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Inquiry::observe(InquiryObserver::class);
    }
}
