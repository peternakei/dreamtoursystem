<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Inquiries\InquiryServiceDetail;
use App\Project\Modules\System\Quotations\QuotationStatus;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkspaceQuotationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Inquiry $inquiry;

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['username' => 'quote-staff@example.test', 'password' => bcrypt('fixture-password'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $this->admin->assignRole(Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_by' => $this->admin->id, 'uuid' => (string) Str::uuid()]));
        $country = Country::create(['name' => 'Tanzania', 'created_by' => $this->admin->id]);
        $tourist = Tourist::create(['name' => 'Quote customer', 'email' => 'quote@example.test', 'phone' => '255700001111', 'country_id' => $country->id]);
        $this->currency = Currency::create(['name' => 'US Dollar', 'short_name' => 'USD', 'symbol' => '$']);
        QuotationStatus::create(['name' => 'Open', 'color' => '#288479']);
        $this->inquiry = Inquiry::create(['tourist_id' => $tourist->id, 'from_date' => '2026-11-01', 'to_date' => '2026-11-03', 'guests' => 2]);
    }

    private function version()
    {
        $this->actingAs($this->admin);

        return app(QuoteBuilderService::class)->createFromInquiry($this->inquiry, ['title' => 'Fixture proposal'], $this->admin->id);
    }

    private function payload($version): array
    {
        $destination = Destination::create(['name' => 'Test park', 'latitude' => -3, 'longitude' => 35, 'description' => 'Fixture', 'created_by' => $this->admin->id, 'is_active' => true]);
        $day = $version->days->first();

        return ['title' => 'Edited proposal', 'currency_id' => $this->currency->id, 'guest_count' => 2,
            'days' => [['uuid' => $day->uuid, 'day_number' => 1, 'title' => 'Park visit', 'destination_id' => $destination->id, 'nights' => 1, 'breakfast' => true, 'lunch' => false, 'dinner' => true, 'activity_ids' => []]],
            'price_lines' => [['description' => 'Agreed trip charge', 'quantity' => 2, 'unit_price' => 100, 'total_price' => 175, 'currency_id' => $this->currency->id, 'source_type' => 'manual', 'source_id' => null, 'traveler_type' => 'Adults', 'is_visible' => true, 'is_optional' => false]],
            'included_terms' => [['title' => 'Custom inclusion', 'description' => 'Retain the authored scope', 'is_visible' => true]], 'excluded_terms' => [],
            'payment_terms' => [['title' => 'Deposit', 'description' => 'Agreed deposit terms', 'is_visible' => true]],
            'hide_price_breakdown' => false, 'hide_total_price' => false, 'hide_terms' => false, 'hide_payment_terms' => false, 'public_url_enabled' => false, 'vat_enabled' => true, 'remove_cover_image' => false];
    }

    public function test_staff_can_view_edit_and_duplicate_without_rendering_a_legacy_layout(): void
    {
        $version = $this->version();
        $version->days->first()->activities()->create(['title' => 'Historical custom activity', 'description' => 'Keep this text', 'is_optional' => false, 'sort_order' => 1, 'created_by' => $this->admin->id]);
        $version->update(['pdf_path' => 'old-snapshot.pdf']);
        $this->getJson('/workspace/quotations/'.$version->quotation->uuid)->assertOk()->assertJsonPath('quotation.current_version.uuid', $version->uuid);
        $this->getJson('/workspace/quotation-versions/'.$version->uuid)->assertOk()->assertJsonStructure(['version', 'destinations', 'currencies', 'activities']);
        $payload = $this->payload($version);
        $this->post('/workspace/quotation-versions/'.$version->uuid, ['_method' => 'PUT', 'payload' => json_encode($payload)], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('status', true);
        $saved = $version->fresh(['days.activities', 'terms', 'paymentTerms', 'priceLines']);
        $this->assertEquals(206.5, $saved->total_amount); // Existing manual line total + 18% VAT.
        $this->assertNull($saved->pdf_path);
        $this->assertSame('Historical custom activity', $saved->days->first()->activities->first()->title);
        $this->assertSame('Retain the authored scope', $saved->terms->first()->description);
        $this->assertSame('Agreed deposit terms', $saved->paymentTerms->first()->description);
        $this->getJson('/workspace/quotation-versions/'.$version->uuid.'/preview')->assertOk()->assertJsonPath('share.public_link_enabled', false);
        $this->postJson('/workspace/quotation-versions/'.$version->uuid.'/share')->assertOk()->assertJsonPath('share.public_link_enabled', true);
        $this->postJson('/workspace/quotations/'.$version->quotation->uuid.'/duplicate')->assertOk()->assertJsonStructure(['version_uuid']);
        $this->assertSame(2, $version->quotation->versions()->count());
        $duplicate = $version->quotation->fresh('currentVersion.days.activities')->currentVersion;
        $this->assertEquals(206.5, $duplicate->total_amount);
        $this->assertTrue($duplicate->vat_enabled);
        $this->assertSame('Historical custom activity', $duplicate->days->first()->activities->first()->title);
        $this->assertFalse($duplicate->public_url_enabled);
    }

    public function test_save_validation_and_service_rules_are_preserved(): void
    {
        $version = $this->version();
        $payload = $this->payload($version);
        $payload['days'][0]['destination_id'] = null;
        $this->putJson('/workspace/quotation-versions/'.$version->uuid, $payload)->assertUnprocessable()->assertJsonValidationErrors('days');
        $this->assertSame('Fixture proposal', $version->fresh()->title);
        InquiryServiceDetail::create(['inquiry_id' => $this->inquiry->id, 'service_type' => 'rental', 'contact' => [], 'request_details' => []]);
        $this->getJson('/workspace/quotation-versions/'.$version->uuid)->assertUnprocessable();
        $this->putJson('/workspace/quotation-versions/'.$version->uuid, $payload)->assertUnprocessable();
        $this->postJson('/workspace/quotations/'.$version->quotation->uuid.'/duplicate')->assertUnprocessable();
    }

    public function test_native_create_uses_existing_inquiry_and_quote_engine(): void
    {
        $this->actingAs($this->admin);
        $payload = ['title' => 'New proposal', 'currency_id' => $this->currency->id];
        $this->postJson('/workspace/inquiries/'.$this->inquiry->uuid.'/quotations', $payload)->assertUnprocessable();
        $this->inquiry->update(['is_approved' => true]);
        $this->getJson('/workspace/inquiries/'.$this->inquiry->uuid.'/quotation-options')->assertOk()->assertJsonStructure(['inquiry', 'trips', 'currencies']);
        $this->postJson('/workspace/inquiries/'.$this->inquiry->uuid.'/quotations', $payload)->assertOk()->assertJsonStructure(['quotation_uuid', 'version_uuid']);
        $this->assertSame(1, $this->inquiry->quotations()->count());
    }

    public function test_workspace_quote_endpoints_require_existing_admin_access(): void
    {
        $uuid = (string) Str::uuid();
        $this->getJson('/workspace/quotations/'.$uuid)->assertUnauthorized();
        $this->postJson('/workspace/quotation-versions/'.$uuid.'/share')->assertUnauthorized();
        $this->admin->removeRole('SuperAdmin');
        $this->actingAs($this->admin)->getJson('/workspace/quotations/'.$uuid)->assertForbidden();
    }

    public function test_old_local_quote_urls_redirect_to_vue_but_document_routes_stay_backend(): void
    {
        $version = $this->version();
        $this->app['env'] = 'local';
        $this->get('/quotations/'.$version->quotation->uuid)->assertRedirect('http://127.0.0.1:5174/quotations/'.$version->quotation->uuid.'/details');
        $this->get('/quotation-versions/'.$version->uuid.'/builder')->assertRedirect('http://127.0.0.1:5174/quotation-versions/'.$version->uuid.'/builder');
        $this->get('/quotation-versions/'.$version->uuid.'/preview')->assertRedirect('http://127.0.0.1:5174/quotation-versions/'.$version->uuid.'/preview');
        $this->get('/inquiries/'.$this->inquiry->uuid.'/quotations/create')->assertRedirect('http://127.0.0.1:5174/inquiries/'.$this->inquiry->uuid.'/quotations/new');
    }
}
