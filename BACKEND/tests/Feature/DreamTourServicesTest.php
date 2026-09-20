<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Bookings\BookingStatus;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Inquiries\Seeders\DreamServicePermissionsSeeder;
use App\Project\Modules\System\Pages\Seeders\DreamContentSeeder;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\TripType;
use App\Project\Modules\System\Vehicles\RentalOffer;
use App\Project\Modules\System\Vehicles\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DreamTourServicesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private int $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['username' => 'staff@example.test', 'password' => bcrypt('test-password'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $this->admin->assignRole(Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_by' => $this->admin->id, 'uuid' => (string) Str::uuid()]));
        $this->country = Country::create(['name' => 'Tanzania', 'created_by' => $this->admin->id])->id;
    }

    private function customer(string $prefix = 'customer'): User
    {
        $t = Tourist::create(['name' => $prefix, 'email' => $prefix.'@example.test', 'phone' => $prefix.'-phone', 'country_id' => $this->country]);

        return User::create(['username' => $t->email, 'password' => bcrypt('test-password'), 'profile' => 'Tourist', 'profile_id' => $t->id]);
    }

    private function rental(): array
    {
        return ['service_type' => 'rental', 'firstName' => 'Alex', 'lastName' => 'Guest', 'email' => 'guest@example.test', 'phone' => '255700000009', 'country' => $this->country, 'message' => 'Wedding; please discuss decoration separately.', 'locale' => 'sw',
            'details' => ['purpose' => 'wedding', 'vehicle_type' => 'luxury', 'start_at' => now()->addDays(10)->format('Y-m-d').'T10:00', 'end_at' => now()->addDays(11)->format('Y-m-d').'T10:00', 'quantity' => 2, 'driver' => 'with_driver', 'pickup' => 'Dar es Salaam', 'dropoff' => 'Dar es Salaam']];
    }

    private function offer(): RentalOffer
    {
        return RentalOffer::create(['title' => 'Wedding cars', 'purpose' => 'wedding', 'vehicle_type' => 'luxury', 'is_published' => true, 'details' => ['extras' => [['code' => 'decoration', 'title' => 'Decoration by quotation']]]]);
    }

    public function test_wedding_request_retains_details_without_fake_trip_ids_and_no_zero_price_booking(): void
    {
        $offer = $this->offer();
        $data = $this->rental();
        $data['details']['offer_uuid'] = $offer->uuid;
        $r = $this->postJson('/api/save-service-inquiry', $data)->assertOk()->assertJsonPath('status', true)
            ->assertJsonPath('data.inquiry.details.quantity', 2)->assertJsonPath('data.inquiry.details.duration_days', 1)
            ->assertJsonPath('data.inquiry.details.estimated_total', null)->assertJsonPath('data.inquiry.offer.uuid', $offer->uuid);
        $inquiry = Inquiry::where('uuid', $r->json('data.inquiry.uuid'))->firstOrFail();
        $this->assertNull($inquiry->trip_type_id);
        $this->assertSame('sw', $inquiry->communication_language);
        $this->assertDatabaseCount('bookings', 0);
        $this->getJson('/api/get-inquiries?email=guest@example.test')->assertJsonCount(0, 'data.inquiries');
    }

    public function test_rental_duration_quantities_and_offer_rules_are_enforced(): void
    {
        $offer = $this->offer();
        $data = $this->rental();
        $data['details']['offer_uuid'] = $offer->uuid;
        $data['details']['duration_days'] = 3;
        $this->postJson('/api/save-service-inquiry', $data)->assertUnprocessable()->assertJsonValidationErrors('details.duration_days');
        unset($data['details']['duration_days']);
        $data['details']['driver'] = 'self_drive';
        $this->postJson('/api/save-service-inquiry', $data)->assertUnprocessable()->assertJsonValidationErrors('details.driver');
        $data['details']['driver'] = 'with_driver';
        $data['details']['quantity'] = 0;
        $this->postJson('/api/save-service-inquiry', $data)->assertUnprocessable()->assertJsonValidationErrors('details.quantity');
        $data['details']['quantity'] = 2;
        $data['details']['extras'] = ['not-offered'];
        $this->postJson('/api/save-service-inquiry', $data)->assertUnprocessable()->assertJsonValidationErrors('details.extras');
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_partial_days_round_up_and_duration_only_has_authoritative_dates(): void
    {
        $data = $this->rental();
        $data['details']['end_at'] = now()->addDays(11)->format('Y-m-d').'T11:00';
        $this->postJson('/api/save-service-inquiry', $data)->assertOk()->assertJsonPath('data.inquiry.details.duration_days', 2);
        unset($data['details']['end_at']);
        $data['details']['duration_days'] = 1;
        $this->postJson('/api/save-service-inquiry', $data)->assertOk()->assertJsonPath('data.inquiry.details.duration_days', 1)->assertJsonPath('data.inquiry.details.end_at', now()->addDays(11)->format('Y-m-d').'T10:00');
    }

    public function test_catalog_filters_publication_locale_and_existing_vehicle_behavior(): void
    {
        $offer = $this->offer();
        $offer->update(['translations' => ['fr' => ['title' => 'Voitures de mariage']]]);
        RentalOffer::create(['title' => 'Private draft', 'purpose' => 'private', 'vehicle_type' => 'sedan']);
        $this->getJson('/api/rental-offers?purpose=wedding&locale=fr&limit=1&offset=0')->assertOk()->assertJsonPath('data.total', 1)->assertJsonPath('data.rental_offers.0.title', 'Voitures de mariage');
        $this->getJson('/api/rental-offers?purpose=private')->assertJsonCount(0, 'data.rental_offers');
        $vehicle = Vehicle::create(['name' => 'Existing safari car', 'capacity' => '6 guests', 'created_by' => $this->admin->id]);
        $this->getJson('/api/rental-vehicles')->assertJsonCount(0, 'data.vehicles');
        $this->actingAs($this->admin, 'web')->putJson('/workspace/vehicles/'.$vehicle->uuid.'/rental', ['is_rental_published' => true, 'rental_specs' => ['vehicle_type' => 'suv', 'seats' => 6, 'transmission' => 'manual', 'fuel_type' => 'diesel']])->assertOk();
        $this->getJson('/api/rental-vehicles?vehicle_type=suv')->assertJsonCount(1, 'data.vehicles');
        $this->assertSame('6 guests', $vehicle->fresh()->capacity);
    }

    public function test_only_verified_owner_or_staff_can_view_service_details(): void
    {
        $a = $this->customer('alice');
        Sanctum::actingAs($a);
        $created = $this->postJson('/api/save-service-inquiry', $this->rental())->assertOk();
        $uuid = $created->json('data.inquiry.uuid');
        $this->getJson('/api/get-service-inquiry/'.$uuid)->assertOk()->assertJsonMissingPath('data.inquiry.operations');
        Sanctum::actingAs($this->customer('bob'));
        $this->getJson('/api/get-service-inquiry/'.$uuid)->assertNotFound();
        $this->getJson('/api/get-service-inquiries')->assertJsonCount(0, 'data.inquiries');
        $this->getJson('/workspace/service-inquiries')->assertUnauthorized();
        $this->actingAs($this->admin, 'web')->getJson('/workspace/service-inquiries/'.$uuid)->assertOk()->assertJsonPath('data.inquiry.service_type', 'rental');
    }

    public function test_ordered_flight_legs_and_passenger_counts_are_retained(): void
    {
        $data = $this->rental();
        $data['service_type'] = 'flight';
        $data['details'] = ['trip_type' => 'multi_city', 'geography' => 'international', 'adults' => 2, 'children' => 1, 'cabin' => 'business', 'legs' => [
            ['origin' => 'DAR', 'destination' => 'DXB', 'departure_date' => now()->addDays(15)->toDateString()],
            ['origin' => 'DXB', 'destination' => 'CDG', 'departure_date' => now()->addDays(20)->toDateString()],
        ]];
        $this->postJson('/api/save-service-inquiry', $data)->assertOk()->assertJsonPath('data.inquiry.guests', 3)->assertJsonPath('data.inquiry.details.legs.1.destination', 'CDG');
        $data['details']['legs'][1]['departure_date'] = now()->addDays(10)->toDateString();
        $this->postJson('/api/save-service-inquiry', $data)->assertUnprocessable()->assertJsonValidationErrors('details.legs');
        $data['details']['trip_type'] = 'round_trip';
        $data['details']['legs'][1]['departure_date'] = now()->addDays(20)->toDateString();
        $this->postJson('/api/save-service-inquiry', $data)->assertUnprocessable()->assertJsonValidationErrors('details.legs');
    }

    public function test_business_and_geography_are_independent_and_legacy_inquiries_keep_contract(): void
    {
        $data = $this->rental();
        $data['service_type'] = 'business';
        $data['startDate'] = now()->addDays(10)->toDateString();
        $data['endDate'] = now()->addDays(12)->toDateString();
        $data['guests'] = 4;
        $data['details'] = ['company' => 'Example Ltd', 'contact_person' => 'Alex', 'purpose' => 'Conference', 'geography' => 'international', 'country_city' => 'Paris', 'accommodation' => 'Hotel', 'selected_services' => ['Airport transfer']];
        $this->postJson('/api/save-service-inquiry', $data)->assertOk()->assertJsonPath('data.inquiry.details.company', 'Example Ltd')->assertJsonPath('data.inquiry.details.geography', 'international');
        $legacy = $data;
        unset($legacy['service_type'], $legacy['details']);
        $legacy['tripType'] = TripType::create(['name' => 'Private', 'color' => '#000000'])->id;
        $r = $this->postJson('/api/save-inquiry', $legacy)->assertOk()->assertJsonPath('status', true)->assertJsonPath('code', 200)->assertJsonPath('data.firstName', 'Alex')->assertJsonMissingPath('data.service_type');
        $this->assertDatabaseHas('inquiries', ['uuid' => $r->json('data.uuid'), 'trip_type_id' => $legacy['tripType']]);
        $this->getJson('/api/get-inquiries?email=guest@example.test')->assertJsonCount(1, 'data.inquiries');
    }

    public function test_cms_uses_existing_pages_with_publication_and_translation_fallback(): void
    {
        $data = ['name' => 'business-travel', 'title' => 'Business travel', 'description' => 'Company travel assistance', 'sub_title' => 'Planning', 'is_published' => false, 'sort_order' => 1, 'sections' => [['key' => 'intro', 'type' => 'hero', 'title' => 'Plan with us', 'text' => 'Talk to the team', 'cta_url' => '/contact']], 'translations' => ['fr' => ['title' => 'Voyages professionnels']]];
        $created = $this->actingAs($this->admin, 'web')->postJson('/workspace/pages-content', $data)->assertOk();
        $uuid = $created->json('data.page.uuid');
        $this->getJson('/api/content-pages/business-travel')->assertNotFound();
        $data['is_published'] = true;
        $this->putJson('/workspace/pages-content/'.$uuid, $data)->assertOk();
        $this->getJson('/api/content-pages/business-travel?locale=fr')->assertOk()->assertJsonPath('data.page.title', 'Voyages professionnels')->assertJsonPath('data.page.description', 'Company travel assistance');
        $data['sections'][0]['cta_url'] = 'javascript:alert(1)';
        $this->putJson('/workspace/pages-content/'.$uuid, $data)->assertUnprocessable();
        $this->assertDatabaseCount('pages', 1);
    }

    public function test_staff_quote_reuses_existing_pricing_and_requires_supply_check_before_booking(): void
    {
        $uuid = $this->postJson('/api/save-service-inquiry', $this->rental())->json('data.inquiry.uuid');
        $currency = Currency::create(['name' => 'US Dollar', 'short_name' => 'USD', 'symbol' => '$']);
        QuotationStatus::create(['name' => 'Open', 'color' => '#000000']);
        BookingStatus::create(['name' => 'Reserved', 'color' => '#000000']);
        $this->actingAs($this->admin, 'web');
        $quote = $this->postJson('/workspace/service-inquiries/'.$uuid.'/quotations', [
            'title' => 'Wedding rental quotation', 'currency_id' => $currency->id, 'valid_until' => now()->addDays(2)->toIso8601String(), 'status' => 'sent', 'vat_enabled' => false,
            'terms' => 'Two cars, driver included. Decoration quoted separately.', 'price_lines' => [['description' => 'Two luxury cars for one day', 'quantity' => 2, 'unit_price' => '100.00']],
        ])->assertOk()->assertJsonPath('data.quotes.0.total_amount', '200.00');
        $q = $quote->json('data.quotation_uuid');
        $this->postJson('/workspace/service-inquiries/'.$uuid.'/book', ['quotation_uuid' => $q, 'customer_agreed' => true])->assertUnprocessable()->assertJsonValidationErrors('supply_verified');
        $this->putJson('/workspace/service-inquiries/'.$uuid, ['is_approved' => true, 'operations' => ['supply_verified' => true]])->assertOk();
        $book = $this->postJson('/workspace/service-inquiries/'.$uuid.'/book', ['quotation_uuid' => $q, 'customer_agreed' => true])->assertOk();
        $this->assertDatabaseHas('bookings', ['uuid' => $book->json('data.booking.uuid'), 'trip_id' => null, 'total_amount' => 200]);
        $this->postJson('/workspace/service-inquiries/'.$uuid.'/book', ['quotation_uuid' => $q, 'customer_agreed' => true])->assertOk();
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_customer_cannot_use_staff_catalog_or_content_endpoints(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer, 'web')->getJson('/workspace/rental-offers')->assertForbidden();
        $this->getJson('/workspace/pages-content')->assertForbidden();
        $this->getJson('/workspace/service-inquiries')->assertForbidden();
        $this->getJson('/workspace/travel-content')->assertForbidden();
    }

    public function test_existing_cart_cannot_accept_rental_or_flight_types(): void
    {
        Sanctum::actingAs($this->customer());
        foreach (['rental_offer', 'vehicle', 'flight'] as $type) {
            $this->postJson('/api/cart/add', ['cartable_type' => $type, 'cartable_uuid' => (string) Str::uuid()])
                ->assertUnprocessable()->assertJsonValidationErrors('cartable_type');
        }
        $this->assertDatabaseCount('bookings', 0);
        $this->getJson('/api/trips')->assertOk()->assertJsonPath('status', true)->assertJsonStructure(['code', 'message', 'data' => ['trips', 'filters']]);
    }

    public function test_flight_issuance_requires_an_actual_booking_and_reference(): void
    {
        $data = $this->rental();
        $data['service_type'] = 'flight';
        $data['details'] = ['trip_type' => 'one_way', 'geography' => 'domestic', 'adults' => 1, 'cabin' => 'economy', 'legs' => [
            ['origin' => 'DAR', 'destination' => 'ZNZ', 'departure_date' => now()->addDays(10)->toDateString()],
        ]];
        $uuid = $this->postJson('/api/save-service-inquiry', $data)->assertOk()->json('data.inquiry.uuid');
        $currency = Currency::create(['name' => 'US Dollar', 'short_name' => 'USD', 'symbol' => '$']);
        QuotationStatus::create(['name' => 'Open', 'color' => '#000000']);
        BookingStatus::create(['name' => 'Reserved', 'color' => '#000000']);
        $this->actingAs($this->admin, 'web');
        $this->putJson('/workspace/service-inquiries/'.$uuid, ['is_approved' => true, 'operations' => ['issuance_state' => 'issued', 'booking_reference' => 'EXAMPLE', 'issued_at' => now()->toIso8601String()]])
            ->assertUnprocessable()->assertJsonValidationErrors('operations.issuance_state');
        $quote = $this->postJson('/workspace/service-inquiries/'.$uuid.'/quotations', [
            'title' => 'Flight offer', 'currency_id' => $currency->id, 'valid_until' => now()->addDay()->toIso8601String(), 'status' => 'sent', 'vat_enabled' => false,
            'terms' => 'Example fare conditions', 'price_lines' => [['description' => 'Flight fare', 'quantity' => 1, 'unit_price' => '80.00']],
            'flight_offer' => ['itinerary' => 'DAR to ZNZ', 'airline' => 'Example airline', 'cabin' => 'Economy', 'baggage' => 'As quoted', 'fare_conditions' => 'As quoted'],
        ])->assertOk();
        $this->postJson('/workspace/service-inquiries/'.$uuid.'/book', ['quotation_uuid' => $quote->json('data.quotation_uuid'), 'customer_agreed' => true])->assertOk();
        $this->putJson('/workspace/service-inquiries/'.$uuid, ['is_approved' => true, 'operations' => ['issuance_state' => 'issued', 'booking_reference' => 'EXAMPLE', 'issued_at' => now()->toIso8601String()]])
            ->assertOk()->assertJsonPath('data.inquiry.fulfilment.issuance_state', 'issued');
        $this->putJson('/workspace/service-inquiries/'.$uuid, ['is_approved' => true, 'operations' => ['issuance_state' => 'cancelled', 'booking_reference' => 'OTHER']])
            ->assertUnprocessable()->assertJsonValidationErrors('operations.booking_reference');
        $this->putJson('/workspace/service-inquiries/'.$uuid, ['is_approved' => true, 'operations' => ['issuance_state' => 'cancelled']])
            ->assertOk()->assertJsonPath('data.inquiry.fulfilment.issuance_state', 'cancelled')
            ->assertJsonPath('data.inquiry.fulfilment.booking_reference', 'EXAMPLE');
    }

    public function test_service_permissions_and_content_seeders_are_additive_and_idempotent(): void
    {
        $this->seed(DreamServicePermissionsSeeder::class);
        $this->seed(DreamContentSeeder::class);
        $this->seed(DreamContentSeeder::class);
        $this->assertDatabaseCount('pages', 6);
        $this->assertDatabaseHas('pages', ['name' => 'home', 'is_published' => false]);
        $this->assertTrue($this->admin->fresh()->hasPermissionTo('manage_rental_offers'));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_staff_can_create_all_service_types_without_claiming_customer_ownership(): void
    {
        $this->actingAs($this->admin, 'web');
        foreach (['rental', 'business', 'international', 'local', 'group', 'safari', 'flight'] as $type) {
            $data = $this->rental();
            $data['service_type'] = $type;
            $data['source'] = 'whatsapp';
            $data['assigned_to'] = $this->admin->id;
            $data['staff_notes'] = 'Follow up tomorrow';
            $data['customer_user_id'] = $this->admin->id;
            $data['user_id'] = $this->admin->id;
            $data['created_by'] = 999999;
            if ($type === 'flight') {
                $data['details'] = ['trip_type' => 'round_trip', 'geography' => 'domestic', 'adults' => 2, 'cabin' => 'economy', 'legs' => [
                    ['origin' => 'DAR', 'destination' => 'JRO', 'departure_date' => now()->addDays(10)->toDateString()],
                    ['origin' => 'JRO', 'destination' => 'DAR', 'departure_date' => now()->addDays(12)->toDateString()],
                ]];
            } elseif ($type !== 'rental') {
                $data['startDate'] = now()->addDays(10)->toDateString();
                $data['endDate'] = now()->addDays(12)->toDateString();
                $data['guests'] = 2;
                $data['details'] = ['purpose' => 'Customer visit', 'company' => 'Example Ltd', 'contact_person' => 'Alex', 'country_city' => 'Nairobi', 'destination_region' => 'Arusha'];
            }
            $response = $this->postJson('/workspace/service-inquiries', $data)->assertOk()
                ->assertJsonPath('data.inquiry.source', 'whatsapp')
                ->assertJsonPath('data.inquiry.created_by', $this->admin->id)
                ->assertJsonPath('data.inquiry.customer_user_id', null)
                ->assertJsonPath('data.inquiry.operations.internal_notes', 'Follow up tomorrow');
            $inquiry = Inquiry::where('uuid', $response->json('data.inquiry.uuid'))->firstOrFail();
            $this->assertNull($inquiry->user_id);
            $this->assertEquals($this->admin->id, $inquiry->assigned_to);
        }
        $this->assertDatabaseCount('inquiries', 7);
        $this->assertDatabaseCount('tourists', 1);
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('quotations', 0);
    }

    public function test_staff_request_permissions_and_catalog_options(): void
    {
        $this->getJson('/workspace/service-inquiries/options')->assertUnauthorized();
        $data = $this->rental() + ['source' => 'phone'];
        $this->postJson('/workspace/service-inquiries', $data)->assertUnauthorized();
        $customer = $this->customer();
        $this->actingAs($customer, 'web')->postJson('/workspace/service-inquiries', $data)->assertForbidden();
        $staff = User::create(['username' => 'limited@example.test', 'password' => bcrypt('test'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $this->actingAs($staff, 'web')->getJson('/workspace/service-inquiries/options')->assertForbidden();
        $this->seed(DreamServicePermissionsSeeder::class);
        $staff->givePermissionTo('manage_service_inquiries');
        $offer = $this->offer();
        RentalOffer::create(['title' => 'Hidden draft', 'purpose' => 'private', 'vehicle_type' => 'sedan']);
        $this->actingAs($staff, 'web')->getJson('/workspace/service-inquiries/options')->assertOk()
            ->assertJsonCount(1, 'data.offers')->assertJsonPath('data.offers.0.uuid', $offer->uuid);
        $this->postJson('/workspace/service-inquiries', $data)->assertOk()->assertJsonPath('data.inquiry.created_by', $staff->id);
    }

    public function test_staff_creation_rejects_invalid_assignment_source_and_conflicting_contacts(): void
    {
        $customer = $this->customer('existing');
        $data = $this->rental() + ['source' => 'website', 'assigned_to' => $customer->id];
        $this->actingAs($this->admin, 'web')->postJson('/workspace/service-inquiries', $data)->assertUnprocessable()->assertJsonValidationErrors(['source', 'assigned_to']);
        $data['source'] = 'office';
        $data['assigned_to'] = null;
        $data['email'] = 'existing@example.test';
        $this->postJson('/workspace/service-inquiries', $data)->assertUnprocessable();
        $this->assertDatabaseCount('inquiries', 0);
        $data['email'] = 'new@example.test';
        $data['details']['quantity'] = 0;
        $this->postJson('/workspace/service-inquiries', $data)->assertUnprocessable()->assertJsonValidationErrors('details.quantity');
        $this->assertDatabaseCount('inquiries', 0);
    }
}
