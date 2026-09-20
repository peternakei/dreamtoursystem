<?php

namespace App\Project\Modules\Core\Dashboard\Services;

use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Invoices\Invoice;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GetUserDashboardDataFormAction
{
    public function build(): array
    {
        $year = (int) now()->year;
        $months = collect(range(1, 12));
        $monthLabels = $months->map(fn($month) => Carbon::create()->month($month)->format('M'))->all();

        $touristsCount = Tourist::query()->count();
        $destinationsCount = Destination::query()->count();
        $tripsCount = Trip::query()->count();
        $bookingsCount = Booking::query()->count();
        $revenueTotal = (float) Invoice::query()->sum('total_amount');

        $quotationStatusCounts = Quotation::query()
            ->join('quotation_statuses', 'quotations.quotation_status_id', '=', 'quotation_statuses.id')
            ->selectRaw('quotation_statuses.name as name, COUNT(quotations.id) as total')
            ->groupBy('quotation_statuses.name')
            ->orderBy('quotation_statuses.name')
            ->get()
            ->map(fn($row) => [
                'name' => Str::headline($row->name),
                'y' => (int) $row->total,
            ])
            ->values()
            ->all();

        $tripCategoryCounts = DB::table('trip_categories')
            ->join('categories', 'trip_categories.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as name, COUNT(trip_categories.id) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn($row) => [
                'name' => $row->name,
                'y' => (int) $row->total,
            ])
            ->values()
            ->all();

        $monthlyRevenueLookup = Invoice::query()
            ->selectRaw('MONTH(invoice_date) as month_number, COALESCE(SUM(total_amount), 0) as total')
            ->whereYear('invoice_date', $year)
            ->groupByRaw('MONTH(invoice_date)')
            ->pluck('total', 'month_number');

        $monthlyBookingsLookup = Booking::query()
            ->selectRaw('MONTH(booking_date) as month_number, COUNT(id) as total')
            ->whereYear('booking_date', $year)
            ->groupByRaw('MONTH(booking_date)')
            ->pluck('total', 'month_number');

        $mapDestinations = Destination::query()
            ->with(['location:id,name', 'region:id,name'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(20)
            ->get()
            ->map(function (Destination $destination) {
                return [
                    'name' => $destination->name,
                    'latitude' => (float) $destination->latitude,
                    'longitude' => (float) $destination->longitude,
                    'description' => Str::limit(strip_tags((string) $destination->description), 120),
                    'location' => $destination->location?->name,
                    'region' => $destination->region?->name,
                ];
            })
            ->values()
            ->all();

        return [
            'year' => $year,
            'summary_cards' => [
                [
                    'label' => 'Tourists',
                    'value' => number_format($touristsCount),
                    'icon' => 'uil-users-alt',
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Destinations',
                    'value' => number_format($destinationsCount),
                    'icon' => 'uil-map-marker',
                    'tone' => 'secondary',
                ],
                [
                    'label' => 'Trips',
                    'value' => number_format($tripsCount),
                    'icon' => 'uil-briefcase-alt',
                    'tone' => 'accent',
                ],
                [
                    'label' => 'Bookings',
                    'value' => number_format($bookingsCount),
                    'icon' => 'uil-ticket',
                    'tone' => 'deep',
                ],
            ],
            'highlights' => [
                [
                    'label' => 'Total Revenue',
                    'value' => 'TZS ' . number_format($revenueTotal, 0),
                ],
                [
                    'label' => 'Published Trips',
                    'value' => number_format(Trip::query()->where('is_published', true)->count()),
                ],
                [
                    'label' => 'Active Destinations',
                    'value' => number_format(Destination::query()->where('is_active', true)->count()),
                ],
                [
                    'label' => 'Open Quotations',
                    'value' => number_format(
                        Quotation::query()
                            ->join('quotation_statuses', 'quotations.quotation_status_id', '=', 'quotation_statuses.id')
                            ->where('quotation_statuses.name', 'open')
                            ->count()
                    ),
                ],
            ],
            'quotation_status_summary' => $quotationStatusCounts,
            'trip_category_summary' => $tripCategoryCounts,
            'monthly_performance' => [
                'categories' => $monthLabels,
                'revenue' => $months->map(fn($month) => (float) ($monthlyRevenueLookup[$month] ?? 0))->all(),
                'bookings' => $months->map(fn($month) => (int) ($monthlyBookingsLookup[$month] ?? 0))->all(),
            ],
            'destinations_map' => $mapDestinations,
        ];
    }

    public function handle()
    {
        $data = $this->build();

        return response()->json([
            'success' => true,
            'message' => 'Data fetched successfully',
            'data' => $data
        ]);
    }
}
