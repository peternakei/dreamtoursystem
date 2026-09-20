<?php

namespace App\Project\Modules\System\Inquiries\Requests\Api;

use App\Project\Modules\System\Vehicles\RentalCatalogController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateServiceInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'service_type' => 'required|in:rental,business,international,local,group,safari,flight',
            'firstName' => 'required|string|max:255', 'lastName' => 'required|string|max:255', 'email' => 'required|email|max:255', 'phone' => 'required|string|max:20',
            'country' => 'required|integer|exists:countries,id', 'message' => 'required|string|max:1000', 'locale' => 'nullable|in:en,fr,sw', 'currency_id' => 'nullable|integer|exists:currencies,id',
            'guests' => (in_array($this->input('service_type'), ['rental', 'flight'], true) ? 'nullable' : 'required').'|integer|min:1', 'trip_uuid' => ['prohibited_if:service_type,rental,flight', 'nullable', 'uuid', Rule::exists('trips', 'uuid')->where('is_published', true)->whereNull('deleted_at')],
            'destinations' => 'nullable|array', 'destinations.*' => 'integer|exists:destinations,id', 'serviceClass' => 'nullable|integer|exists:service_classes,id',
            'budget' => 'nullable|string|max:255',
        ];
        if ($this->input('service_type') === 'rental') {
            return $rules + [
                'details' => 'required|array:offer_uuid,vehicle_uuid,purpose,vehicle_type,start_at,end_at,duration_days,quantity,passengers,driver,fuel,pickup,dropoff,extras,timezone',
                'details.offer_uuid' => 'nullable|uuid', 'details.vehicle_uuid' => 'nullable|uuid', 'details.purpose' => 'required|in:corporate,private,wedding,event',
                'details.vehicle_type' => ['required', Rule::in(RentalCatalogController::TYPES)],
                'details.start_at' => 'required|date_format:Y-m-d\TH:i|after:now', 'details.end_at' => 'nullable|required_without:details.duration_days|date_format:Y-m-d\TH:i|after:details.start_at',
                'details.duration_days' => 'nullable|required_without:details.end_at|integer|min:1', 'details.quantity' => 'required|integer|min:1',
                'details.passengers' => 'nullable|integer|min:1', 'details.driver' => 'required|in:with_driver,self_drive', 'details.fuel' => 'nullable|in:included,excluded',
                'details.pickup' => 'required|string|max:255', 'details.dropoff' => 'required|string|max:255',
                'details.extras' => 'nullable|array', 'details.extras.*' => 'string|distinct|max:80', 'details.timezone' => ['nullable', Rule::in([config('app.timezone')])],
            ];
        }
        if ($this->input('service_type') === 'flight') {
            return $rules + [
                'details' => 'required|array:trip_type,geography,legs,adults,children,infants,cabin,flexibility,airline_preference,group_travel',
                'details.trip_type' => 'required|in:one_way,round_trip,multi_city', 'details.geography' => 'required|in:domestic,international',
                'details.legs' => 'required|array|min:1', 'details.legs.*' => 'array:origin,destination,departure_date',
                'details.legs.*.origin' => 'required|string|max:255', 'details.legs.*.destination' => 'required|string|max:255',
                'details.legs.*.departure_date' => 'required|date_format:Y-m-d|after:today',
                'details.adults' => 'required|integer|min:1', 'details.children' => 'nullable|integer|min:0', 'details.infants' => 'nullable|integer|min:0',
                'details.cabin' => 'required|in:economy,premium_economy,business,first', 'details.flexibility' => 'nullable|string|max:255',
                'details.airline_preference' => 'nullable|string|max:255', 'details.group_travel' => 'nullable|boolean',
            ];
        }

        return $rules + [
            'startDate' => 'required|date_format:Y-m-d|after:today', 'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate', 'guests' => 'required|integer|min:1',
            'details' => 'required|array:company,industry,contact_person,purpose,geography,country_city,sector,destination_region,meeting_details,transport,accommodation,places_of_interest,selected_services,customized,route,duration_days',
            'details.company' => 'required_if:service_type,business|nullable|string|max:255', 'details.contact_person' => 'required_if:service_type,business|nullable|string|max:255',
            'details.industry' => 'nullable|string|max:255', 'details.purpose' => 'required|string|max:255', 'details.geography' => 'nullable|in:local,international',
            'details.country_city' => 'required_if:service_type,international|nullable|string|max:255', 'details.destination_region' => 'required_if:service_type,local|nullable|string|max:255',
            'details.sector' => 'nullable|in:agriculture,mining,investment,trade,tourism', 'details.meeting_details' => 'nullable|string',
            'details.transport' => 'nullable|string', 'details.accommodation' => 'nullable|string', 'details.places_of_interest' => 'nullable|string',
            'details.selected_services' => 'nullable|array', 'details.selected_services.*' => 'string|max:255', 'details.customized' => 'nullable|boolean',
            'details.route' => 'nullable|string', 'details.duration_days' => 'nullable|integer|min:1',
        ];
    }
}
