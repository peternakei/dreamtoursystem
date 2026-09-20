<?php

namespace App\Project\Modules\System\Bookings\Services\Api;

use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\ExchangeRates\ExchangeRate;
use App\Project\Modules\System\Invoices\Invoice;
use App\Project\Modules\System\Seasons\Budget;
use App\Project\Modules\System\Seasons\SeasonDate;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripGroup;
use App\Project\Modules\Core\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaveBookingFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        //save tourist
        $exists = Tourist::where('phone', 'like', '%' . $request->phone . '%')->orWhere('phone', 'like', '%' . $request->email . '%')->get();
        if (count($exists) > 0) {
            $tourist = Tourist::find($exists[0]['id']);
        } else {
            $tourist = Tourist::create([
                'name' => $request->fname . ' ' . $request->lname,
                'gender_id' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                'country_id' => $request->country,
                'address' => $request->address ?? '',
            ]);

            if (!$tourist) {
                return response()->json([
                    'status' => false,
                    'code' => 100,
                    'message' => 'Failed to save tourist details'
                ]);
            }
                //save logins
            $logins =  User::create([
                'username' => $tourist->email,
                'password' => bcrypt($tourist->email),
                'profile' => 'Tourist',
                'profile_id' => $tourist->id,
                ]);
            
            if (!$logins) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'code' => 100,
                    'message' => 'Failed to save user details'
                ]);
            }
        

        }

        $trip = Trip::where('uuid', $request->trip_uuid)->first();

        $guestCount = max((int) $request->guest_count, 1);
        $budgetQuantities = collect([$guestCount, $this->resolveGuestBucket($guestCount)])
            ->unique()
            ->values();

        $start = date('Y-m-d', strtotime($request->start_date));

        $seasonRow = SeasonDate::whereDate('start_date', '<=', $start)
            ->whereDate('end_date', '>=', $start)
            ->where('is_active', true)
            ->first();

        $seasonId = null;
        if ($seasonRow) {
            $seasonId = (int) $seasonRow->season_id;
        } elseif ($request->filled('season_id')) {
            // BFF / client sends season aligned to trip budgets when calendar SeasonDate misses
            $seasonId = (int) $request->season_id;
        } else {
            $fallbackSeason = SeasonDate::where('is_active', true)->orderBy('id')->first();
            $seasonId = $fallbackSeason ? (int) $fallbackSeason->season_id : null;
        }

        if (!$seasonId) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 422,
                'message' => 'No season is configured for this travel date, and no season_id was provided.',
            ], 422);
        }
        $classId = (int) $request->class_id;

        $budget = Budget::query()
            ->where('season_id', $seasonId)
            ->where('service_class_id', $classId)
            ->where('trip_id', $trip->id)
            ->where('is_active', true)
            ->whereIn('quantity', $budgetQuantities)
            ->get()
            ->sortBy(fn (Budget $budget) => $budgetQuantities->search((int) $budget->quantity))
            ->first();

        if (!$budget) {
            $budget = Budget::query()
                ->where('season_id', $seasonId)
                ->where('trip_id', $trip->id)
                ->where('is_active', true)
                ->whereIn('quantity', $budgetQuantities)
                ->get()
                ->sortBy(fn (Budget $budget) => $budgetQuantities->search((int) $budget->quantity))
                ->first();
        }

        if (!$budget) {
            $budget = Budget::query()
                ->where('trip_id', $trip->id)
                ->where('is_active', true)
                ->whereIn('quantity', $budgetQuantities)
                ->get()
                ->sortBy(fn (Budget $budget) => $budgetQuantities->search((int) $budget->quantity))
                ->first();
        }

        if (!$budget) {
            $budget = Budget::query()
                ->where('season_id', $seasonId)
                ->where('service_class_id', $classId)
                ->where('trip_id', $trip->id)
                ->where('is_active', true)
                ->orderBy('quantity')
                ->first();
        }

        if (!$budget) {
            $budget = Budget::query()
                ->where('trip_id', $trip->id)
                ->where('is_active', true)
                ->orderBy('quantity')
                ->first();
        }

        if (!$budget) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 422,
                'message' => 'No price (budget) found for this trip, season, class, and group size.',
            ], 422);
        }

        $currencyId = Currency::query()->where('short_name', 'USD')->value('id') ?? 2;
        $exchangeRate = ExchangeRate::where(['currency_id' => $currencyId, 'is_active' => true])->value('rate') ?? 1;

        $amount = (int) $request->guest_count * (float) $budget->price;
        $vat = $amount * 0.18;
        $total = $vat + $amount;

        //save booking
        $bookingData = [
            'tourist_id' => $tourist->id,
            'trip_id' => $trip->id,
            'booking_date' => $request->start_date,
            'amount' => $amount,
            'vat_amount' => $vat,
            'total_amount' => $total,
            'guest_count' => $request->guest_count,
            'currency_id' => $currencyId,
            'remarks' => $request->note,
            'booking_status_id' => 2,
        ];

        if ($trip->trip_type_id == 2) {
            $bookingData['trip_group_id'] = TripGroup::where('uuid', $request->trip_group_uuid)->value('id');
            $bookingData['booking_type_id'] = 2;
        } else {
            $bookingData['booking_type_id'] = 1;
        }

        $save = Booking::create($bookingData);

        if (!$save) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to save booking'
            ]);
        }

        //save invoice
        $saveInvoice = Invoice::create([
            'tourist_id' => $tourist->id,
            'booking_id' => $save->id,
            'amount' => $amount,
            'vat_amount' => $vat,
            'invoice_date' => date('Y-m-d'),
            'total_amount' => $total,
            'currency_id' => $currencyId,
            'exchange_rate' => $exchangeRate,
            'remarks' => $request->note,
            'invoice_status_id' => 2,
        ]);

        if (!$saveInvoice) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to save booking invoice'
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'code' => 201,
            'message' => 'Booking reserved successfully',
            'data' => [
                'booking_id' => $save->id,
                'booking_reference' => $save->booking_number,
                'booking_type' => $save->bookingType->name,
                'trip_uuid' => $trip->uuid,
                'guest_count' => $request->guest_count,
                'class_id' => $request->class_id,
                'start_date' => $request->start_date,
                'total_price' => $total,
                'currency' => $save->currency->short_name,
                'payment_status' => $saveInvoice->status->name,
                'customer_name' => $tourist->name,
                'trip_name' => $trip->name
            ]
        ]);
    }

    private function resolveGuestBucket(int $guestCount): int
    {
        if ($guestCount <= 3) {
            return 2;
        }

        if ($guestCount <= 5) {
            return 4;
        }

        return 6;
    }
}
