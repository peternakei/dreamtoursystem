<?php

namespace App\Project\Modules\System\Vehicles;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Pages\ContentSupport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentalCatalogController extends Controller
{
    public const TYPES = ['sedan', 'suv', 'minibus', 'van', 'coaster', 'luxury', 'pickup', 'bus'];

    public function vehicles(Request $request)
    {
        $input = $request->validate(['vehicle_type' => ['nullable', Rule::in(self::TYPES)], 'locale' => 'nullable|in:en,fr,sw', 'limit' => 'nullable|integer|min:1|max:100', 'offset' => 'nullable|integer|min:0']);
        $query = Vehicle::where('is_active', true)->where('is_rental_published', true)->with('media');
        if (! empty($input['vehicle_type'])) {
            $query->where('rental_specs->vehicle_type', $input['vehicle_type']);
        }

        return ContentSupport::listing($query->orderBy('sort_order')->orderBy('id'), $request, 'vehicles', fn ($v) => self::vehicleResource($v, $input['locale'] ?? 'en'));
    }

    public static function vehicleResource(Vehicle $vehicle, string $locale = 'en'): array
    {
        return ContentSupport::localized([
            'uuid' => $vehicle->uuid, 'name' => $vehicle->name, 'description' => $vehicle->description,
            'capacity' => $vehicle->capacity, 'specifications' => $vehicle->rental_specs,
            'images' => ContentSupport::media($vehicle), 'availability' => 'manual_verification_required',
        ], $vehicle->translations, $locale);
    }

    public function offers(Request $request)
    {
        $input = $request->validate(['purpose' => 'nullable|in:corporate,private,wedding,event', 'vehicle_type' => ['nullable', Rule::in(self::TYPES)], 'featured' => 'nullable|boolean', 'locale' => 'nullable|in:en,fr,sw', 'limit' => 'nullable|integer|min:1|max:100', 'offset' => 'nullable|integer|min:0']);
        $query = RentalOffer::where('is_active', true)->where('is_published', true)->with('media');
        foreach (['purpose', 'vehicle_type'] as $key) {
            if (! empty($input[$key])) {
                $query->where($key, $input[$key]);
            }
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        return ContentSupport::listing($query->orderBy('sort_order')->orderBy('id'), $request, 'rental_offers', fn ($offer) => self::offerResource($offer, $input['locale'] ?? 'en'));
    }

    public function show(Request $request, string $uuid)
    {
        $request->validate(['locale' => 'nullable|in:en,fr,sw']);
        $offer = RentalOffer::where('uuid', $uuid)->where('is_active', true)->where('is_published', true)->firstOrFail();

        return ContentSupport::success(['rental_offer' => self::offerResource($offer, $request->input('locale', 'en'))]);
    }

    public static function offerResource(RentalOffer $offer, string $locale = 'en'): array
    {
        return ContentSupport::localized([
            'uuid' => $offer->uuid, 'title' => $offer->title, 'description' => $offer->description, 'purpose' => $offer->purpose,
            'vehicle_type' => $offer->vehicle_type, 'vehicle_uuids' => $offer->vehicle_uuids ?? [],
            'price_unit' => $offer->price_unit, 'price' => null, 'quotation_required' => true,
            'driver_policy' => $offer->driver_policy, 'fuel_policy' => $offer->fuel_policy,
            'details' => $offer->details ?? [], 'is_featured' => $offer->is_featured,
            'images' => ContentSupport::media($offer), 'availability' => 'manual_verification_required',
        ], $offer->translations, $locale);
    }

    public function adminIndex()
    {
        return ContentSupport::success(['rental_offers' => RentalOffer::with('media')->orderBy('sort_order')->get()->map(fn ($o) => array_merge($o->toArray(), ['images' => ContentSupport::media($o)])), 'vehicles' => Vehicle::orderBy('name')->get()->map(fn ($v) => ['uuid' => $v->uuid, 'name' => $v->name, 'rental_specs' => $v->rental_specs, 'translations' => $v->translations, 'is_rental_published' => $v->is_rental_published]), 'vehicle_types' => self::TYPES]);
    }

    public function save(Request $request, ?string $uuid = null)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255', 'description' => 'nullable|string', 'purpose' => 'required|in:corporate,private,wedding,event',
            'vehicle_type' => ['required', Rule::in(self::TYPES)], 'vehicle_uuids' => 'present|array', 'vehicle_uuids.*' => ['uuid', Rule::exists('vehicles', 'uuid')->whereNull('deleted_at')],
            'price_unit' => 'required|in:day,hour,transfer,package', 'driver_policy' => 'required|in:included,excluded,selectable', 'fuel_policy' => 'required|in:included,excluded,selectable',
            'is_active' => 'required|boolean', 'is_published' => 'required|boolean', 'is_featured' => 'required|boolean', 'sort_order' => 'required|integer|min:0',
            'details' => 'nullable|array:duration,inclusions,exclusions,terms,mileage,extras,service_locations',
            'details.duration' => 'nullable|string|max:255', 'details.inclusions' => 'nullable|string', 'details.exclusions' => 'nullable|string', 'details.terms' => 'nullable|string', 'details.mileage' => 'nullable|string',
            'details.service_locations' => 'nullable|array', 'details.service_locations.*' => 'string|max:255',
            'details.extras' => 'nullable|array', 'details.extras.*' => 'array:code,title,description',
            'details.extras.*.code' => 'required|string|alpha_dash|distinct|max:80', 'details.extras.*.title' => 'required|string|max:255', 'details.extras.*.description' => 'nullable|string',
            ...ContentSupport::translationRules(['title', 'description']),
        ]);
        $offer = $uuid ? RentalOffer::where('uuid', $uuid)->firstOrFail() : new RentalOffer;
        $offer->fill($data);
        $offer->{$offer->exists ? 'updated_by' : 'created_by'} = $request->user()->id;
        $offer->save();

        return ContentSupport::success(['rental_offer' => $offer], 'Rental offer saved');
    }

    public function saveVehicle(Request $request, string $uuid)
    {
        $vehicle = Vehicle::where('uuid', $uuid)->firstOrFail();
        $data = $request->validate([
            'is_rental_published' => 'required|boolean', 'rental_specs' => 'required|array:vehicle_type,make,model,seats,transmission,fuel_type,features,luggage_capacity,service_locations',
            'rental_specs.vehicle_type' => ['required', Rule::in(self::TYPES)], 'rental_specs.make' => 'nullable|string|max:255', 'rental_specs.model' => 'nullable|string|max:255',
            'rental_specs.seats' => 'required|integer|min:1', 'rental_specs.transmission' => 'required|in:manual,automatic', 'rental_specs.fuel_type' => 'required|string|max:80',
            'rental_specs.features' => 'nullable|array', 'rental_specs.features.*' => 'string|max:255', 'rental_specs.luggage_capacity' => 'nullable|string|max:255',
            'rental_specs.service_locations' => 'nullable|array', 'rental_specs.service_locations.*' => 'string|max:255', ...ContentSupport::translationRules(['name', 'description']),
        ]);
        $vehicle->update($data + ['updated_by' => $request->user()->id]);

        return ContentSupport::success(['vehicle' => self::vehicleResource($vehicle)], 'Rental specifications saved');
    }
}
