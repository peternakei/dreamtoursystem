<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\System\Bookings\Services\Api\SaveBookingFormAction;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Bookings\BookingStatus;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;

class QuoteBookingService
{
    /**
     * Book a quotation version as a confirmed booking. The quote already holds
     * the finalized price (set by the agent in the builder), so we always book
     * directly off the version's stored amounts and currency — never re-pricing
     * through the trip's budget matrix. The trip link is preserved when the
     * quote was built from one, so the booking remains tied to the trip.
     *
     * @return array{ok: bool, booking_id?: int|null, booking_reference?: ?string, message?: string}
     */
    public function book(
        QuotationVersion $version,
        array $validated,
        Request $request,
        SaveBookingFormAction $saveAction
    ): array {
        $version->loadMissing(['trip', 'quotation.inquiry.tourist']);
        if ($version->quotation?->inquiry?->serviceDetails) {
            return ['ok' => false, 'message' => 'This service requires staff verification and an agreed service quotation. Contact the team to confirm.'];
        }
        $existingTourist = $version->quotation?->inquiry?->tourist;

        $validated['lname'] = $validated['lname'] ?? '';
        $validated['phone'] = $validated['phone'] ?: ($existingTourist?->phone ?? '');
        $validated['country'] = $validated['country'] ?? '';
        $validated['guest_count'] = (int) ($validated['guest_count'] ?: ($version->guest_count ?: 1));
        $validated['start_date'] = $validated['start_date']
            ?: optional($version->start_date)->format('Y-m-d')
            ?: now()->addDays(30)->format('Y-m-d');
        $validated['note'] = $validated['note'] ?? '';

        return $this->bookFromVersionDirectly($version, $validated);
    }

    protected function bookFromVersionDirectly(QuotationVersion $version, array $validated): array
    {
        $tourist = Tourist::where('email', $validated['email'])
            ->orWhere('phone', $validated['phone'])
            ->first();

        if (!$tourist) {
            $tourist = Tourist::create([
                'name' => trim($validated['fname'] . ' ' . $validated['lname']),
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => '',
            ]);

            User::firstOrCreate(
                ['username' => $validated['email']],
                [
                    'password' => bcrypt($validated['email']),
                    'profile' => 'Tourist',
                    'profile_id' => $tourist->id,
                ]
            );
        }

        $bookingStatusId = BookingStatus::query()
            ->whereRaw('LOWER(name) = ?', ['reserved'])
            ->value('id')
            ?? BookingStatus::query()->orderBy('id')->value('id');

        if (!$bookingStatusId) {
            return [
                'ok' => false,
                'message' => 'Booking statuses are not configured. The team will follow up directly.',
            ];
        }

        $amount = (float) ($version->amount ?? 0);
        $vat = (float) ($version->vat_amount ?? 0);
        $total = (float) ($version->total_amount ?? ($amount + $vat));
        $currencyId = $version->currency_id ?: Currency::where('short_name', 'USD')->value('id');

        $booking = Booking::create([
            'tourist_id' => $tourist->id,
            'trip_id' => $version->trip?->id,
            'booking_type_id' => 1,
            'booking_date' => $validated['start_date'],
            'guest_count' => $validated['guest_count'],
            'amount' => $amount,
            'vat_amount' => $vat,
            'total_amount' => $total,
            'currency_id' => $currencyId,
            'remarks' => $validated['note'],
            'comments' => 'Booked from quotation ' . ($version->reference_number ?: $version->uuid),
            'booking_status_id' => $bookingStatusId,
        ]);

        if (!$booking) {
            return [
                'ok' => false,
                'message' => 'Booking could not be saved. Please try again or contact the team.',
            ];
        }

        $version->update(['booking_id' => $booking->id]);

        return [
            'ok' => true,
            'booking_id' => $booking->id,
            'booking_reference' => $booking->booking_number ?: $booking->uuid,
        ];
    }
}
