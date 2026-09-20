<?php

namespace App\Project\Modules\System\Inquiries\Services;

use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Inquiries\ServiceAccess;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Vehicles\RentalCatalogController;
use App\Project\Modules\System\Vehicles\RentalOffer;
use App\Project\Modules\System\Vehicles\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceInquiryService
{
    public static function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public function create(array $data, $user): Inquiry
    {
        if ($user && ($user->profile !== 'Tourist' || ! $user->is_active)) {
            abort(403, 'A customer account is required.');
        }
        $detail = $data['details'];
        $snapshot = null;
        $guests = $data['guests'] ?? 1;
        if ($data['service_type'] === 'rental') {
            $start = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $detail['start_at'], config('app.timezone'))->startOfMinute();
            if (! empty($detail['duration_days']) && $detail['duration_days'] > $start->diffInDays(CarbonImmutable::parse('9999-12-31'))) {
                self::invalid('details.duration_days', 'Duration exceeds the supported calendar range.');
            }
            $end = ! empty($detail['end_at']) ? CarbonImmutable::createFromFormat('Y-m-d\TH:i', $detail['end_at'], config('app.timezone'))->startOfMinute() : $start->addDays($detail['duration_days']);
            $days = (int) ceil(($end->getTimestamp() - $start->getTimestamp()) / 86400);
            if (isset($detail['duration_days']) && (int) (int) $detail['duration_days'] !== $days) {
                self::invalid('details.duration_days', 'Duration does not match the submitted dates.');
            }
            $detail['start_at'] = $start->format('Y-m-d\TH:i');
            $detail['end_at'] = $end->format('Y-m-d\TH:i');
            $detail['duration_days'] = $days;
            $detail['timezone'] = config('app.timezone');
            $detail['day_count_rule'] = 'elapsed_24_hours_rounded_up';
            $detail['estimated_total'] = null;
            $detail['quotation_required'] = true;
            $offer = null;
            if (! empty($detail['offer_uuid'])) {
                $offer = RentalOffer::where('uuid', $detail['offer_uuid'])->where('is_active', true)->where('is_published', true)->first();
                if (! $offer) {
                    self::invalid('details.offer_uuid', 'Select an active published rental offer.');
                }
                if ($offer->purpose !== $detail['purpose'] || $offer->vehicle_type !== $detail['vehicle_type']) {
                    self::invalid('details.offer_uuid', 'Purpose and vehicle type must match the selected offer.');
                }
                if (($offer->driver_policy === 'included' && $detail['driver'] !== 'with_driver') || ($offer->driver_policy === 'excluded' && $detail['driver'] !== 'self_drive')) {
                    self::invalid('details.driver', 'Driver selection is not available for this offer.');
                }
                if ($offer->fuel_policy === 'selectable' && empty($detail['fuel'])) {
                    self::invalid('details.fuel', 'Choose a fuel option.');
                }
                if ($offer->fuel_policy !== 'selectable' && isset($detail['fuel']) && $detail['fuel'] !== $offer->fuel_policy) {
                    self::invalid('details.fuel', 'Fuel selection does not match this offer.');
                }
                $detail['fuel'] = $detail['fuel'] ?? $offer->fuel_policy;
                if (array_diff($detail['extras'] ?? [], array_column($offer->details['extras'] ?? [], 'code'))) {
                    self::invalid('details.extras', 'Choose extras offered by the selected rental package.');
                }
                $snapshot = RentalCatalogController::offerResource($offer);
            } elseif (! empty($detail['extras'])) {
                self::invalid('details.extras', 'Select an offer before selecting its extras; other requests belong in your notes.');
            }
            if (! empty($detail['vehicle_uuid'])) {
                $vehicle = Vehicle::where('uuid', $detail['vehicle_uuid'])->where('is_active', true)->where('is_rental_published', true)->first();
                if (! $vehicle || ($vehicle->rental_specs['vehicle_type'] ?? null) !== $detail['vehicle_type']) {
                    self::invalid('details.vehicle_uuid', 'Select a published vehicle of the requested type.');
                }
                if ($offer && $offer->vehicle_uuids && ! in_array($vehicle->uuid, $offer->vehicle_uuids, true)) {
                    self::invalid('details.vehicle_uuid', 'Vehicle is not offered by this rental package.');
                }
                $snapshot['selected_vehicle'] = RentalCatalogController::vehicleResource($vehicle);
            }
            $from = $start->toDateString();
            $to = $end->toDateString();
            $guests = $detail['passengers'] ?? $guests;
        } elseif ($data['service_type'] === 'flight') {
            $legs = $detail['legs'];
            $count = count($legs);
            $type = $detail['trip_type'];
            if (($type === 'one_way' && $count !== 1) || ($type === 'round_trip' && $count !== 2) || ($type === 'multi_city' && $count < 2)) {
                self::invalid('details.legs', 'Flight legs do not match the trip type.');
            }
            foreach ($legs as $i => $leg) {
                if (strcasecmp(trim($leg['origin']), trim($leg['destination'])) === 0) {
                    self::invalid('details.legs', 'Origin and destination must differ.');
                }
                if ($i && $leg['departure_date'] < $legs[$i - 1]['departure_date']) {
                    self::invalid('details.legs', 'Flight legs must be in date order.');
                }
            }
            if ($type === 'round_trip' && (strcasecmp(trim($legs[0]['origin']), trim($legs[1]['destination'])) || strcasecmp(trim($legs[0]['destination']), trim($legs[1]['origin'])))) {
                self::invalid('details.legs', 'The return leg must reverse the outward journey.');
            }
            $guests = $detail['adults'] + ($detail['children'] ?? 0) + ($detail['infants'] ?? 0);
            if (isset($data['guests']) && (int) $data['guests'] !== $guests) {
                self::invalid('guests', 'Passenger total must match the flight passenger counts.');
            }
            $from = $legs[0]['departure_date'];
            $to = $legs[$count - 1]['departure_date'];
        } else {
            $from = $data['startDate'];
            $to = $data['endDate'];
            $days = (int) CarbonImmutable::parse($from)->diffInDays(CarbonImmutable::parse($to)) + 1;
            if (isset($detail['duration_days']) && $detail['duration_days'] !== $days) {
                self::invalid('details.duration_days', 'Travel duration must match inclusive start/end dates.');
            }
            $detail['duration_days'] = $days;
        }
        if (! empty($data['trip_uuid'])) {
            $trip = Trip::where('uuid', $data['trip_uuid'])->where('is_published', true)->whereHas('tripStatus', fn ($q) => $q->where('name', 'Approved'))->first();
            if (! $trip) {
                self::invalid('trip_uuid', 'Select an approved published trip.');
            }
            $snapshot = ['trip_uuid' => $trip->uuid, 'title' => $trip->name, 'service_details' => $trip->service_details];
        }
        if ($guests > 2147483647) {
            self::invalid('guests', 'Guest count exceeds the database integer capacity.');
        }

        return DB::transaction(function () use ($data, $user, $detail, $snapshot, $from, $to, $guests) {
            if ($user) {
                $tourist = Tourist::findOrFail($user->profile_id);
            } else {
                $tourist = Tourist::where('email', $data['email'])->where('phone', $data['phone'])->first();
                if (! $tourist && Tourist::where('email', $data['email'])->orWhere('phone', $data['phone'])->exists()) {
                    self::invalid('email', 'Contact information could not be matched. Sign in or contact our team.');
                }
                $tourist ??= Tourist::create(['name' => $data['firstName'].' '.$data['lastName'], 'email' => $data['email'], 'phone' => $data['phone'], 'country_id' => $data['country'], 'address' => '']);
            }
            $inquiry = Inquiry::create([
                'tourist_id' => $tourist->id, 'description' => htmlspecialchars($data['message']), 'client_message' => $data['message'],
                'tour_title' => $snapshot['title'] ?? ucfirst($data['service_type']).' request', 'from_date' => $from, 'to_date' => $to,
                'destinations' => $data['destinations'] ?? null, 'service_class_id' => $data['serviceClass'] ?? null, 'guests' => $guests, 'budget' => $data['budget'] ?? null,
                'status' => 'pending', 'communication_language' => $data['locale'] ?? 'en', 'source' => 'website', 'received_at' => now(),
                'user_id' => $user?->id, 'created_by' => $user?->id,
            ]);
            $inquiry->serviceDetails()->create([
                'service_type' => $data['service_type'], 'customer_user_id' => $user?->id, 'locale' => $data['locale'] ?? 'en', 'currency_id' => $data['currency_id'] ?? null,
                'contact' => array_intersect_key($data, array_flip(['firstName', 'lastName', 'email', 'phone', 'country'])),
                'request_details' => $detail, 'offer_snapshot' => $snapshot,
            ]);

            return $inquiry->load('serviceDetails');
        });
    }

    public function createForStaff(array $data, $staff): Inquiry
    {
        abort_unless(ServiceAccess::allowed($staff), 403);

        return DB::transaction(function () use ($data, $staff) {
            // Staff record a customer request, without impersonating a customer login.
            $inquiry = $this->create($data, null);
            $inquiry->update(['source' => $data['source'], 'created_by' => $staff->id, 'assigned_to' => $data['assigned_to'] ?? null]);
            $inquiry->serviceDetails->update(['operations' => ['internal_notes' => $data['staff_notes'] ?? null]]);

            return $inquiry->fresh('serviceDetails');
        });
    }

    public static function resource(Inquiry $inquiry, bool $staff = false): array
    {
        $service = $inquiry->serviceDetails;
        $data = ['uuid' => $inquiry->uuid, 'inquiry_code' => $inquiry->inquiry_code, 'status' => $inquiry->status, 'is_approved' => $inquiry->is_approved,
            'service_type' => $service->service_type, 'locale' => $service->locale, 'currency_id' => $service->currency_id,
            'contact' => $service->contact, 'details' => $service->request_details, 'offer' => $service->offer_snapshot,
            'startDate' => $inquiry->from_date?->format('Y-m-d'), 'endDate' => $inquiry->to_date?->format('Y-m-d'), 'guests' => $inquiry->guests,
            'message' => $inquiry->client_message ?? $inquiry->description, 'budget' => $inquiry->budget, 'agreed_price' => $service->agreed_price,
            'booking_id' => $service->booking_id, 'created_at' => $inquiry->created_at,
            'fulfilment' => array_intersect_key($service->operations ?? [], array_flip(['issuance_state', 'booking_reference', 'issued_at', 'customer_notes', 'cancellation_requested'])),
        ];
        if ($staff) {
            $data += ['source' => $inquiry->source, 'created_by' => $inquiry->created_by, 'operations' => $service->operations, 'assigned_to' => $inquiry->assigned_to, 'comments' => $inquiry->comments, 'customer_user_id' => $service->customer_user_id];
        }

        return $data;
    }
}
