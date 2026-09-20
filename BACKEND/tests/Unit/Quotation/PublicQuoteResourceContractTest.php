<?php

namespace Tests\Unit\Quotation;

use App\Project\Modules\System\Bookings\Requests\Api\SaveBookingFormRequest;
use App\Http\Resources\PublicQuoteResource;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Contract test: every field SaveBookingFormRequest validates as `required`
 * MUST appear in PublicQuoteResource::booking_prefill, and field NAMES must
 * match exactly so the public site can post the prefill shape directly to
 * POST /api/save_booking with no remapping. Optional fields validated by the
 * action (address, season_id) are also asserted as PRESENT so the prefill
 * is a complete drop-in.
 *
 * No DB. Models are constructed in-memory and relations stubbed via
 * setRelation() so this runs hermetically in any environment.
 */
class PublicQuoteResourceContractTest extends TestCase
{
    public function test_prefill_keys_match_save_booking_form_request_rules(): void
    {
        $request = new SaveBookingFormRequest();
        $rules = array_keys($request->rules());

        $resource = new PublicQuoteResource($this->buildVersionFixture());
        $payload = $resource->additional($this->fakeContext())->toArray(Request::create('/'));

        $this->assertArrayHasKey('booking_prefill', $payload);
        $prefill = $payload['booking_prefill'];

        foreach ($rules as $field) {
            $this->assertArrayHasKey(
                $field,
                $prefill,
                "Prefill is missing the '{$field}' field that SaveBookingFormRequest validates."
            );
        }

        // Also asserted: action-level optional fields the prefill should populate.
        foreach (['address', 'season_id'] as $optional) {
            $this->assertArrayHasKey(
                $optional,
                $prefill,
                "Prefill should populate the '{$optional}' field that SaveBookingFormAction reads."
            );
        }
    }

    public function test_prefill_values_are_taken_from_the_quotation_version_and_tourist(): void
    {
        $resource = new PublicQuoteResource($this->buildVersionFixture());
        $payload = $resource->additional($this->fakeContext())->toArray(Request::create('/'));
        $prefill = $payload['booking_prefill'];

        $this->assertSame('trip-uuid-abc', $prefill['trip_uuid']);
        $this->assertSame(2, $prefill['class_id']);
        $this->assertSame(2, $prefill['guest_count']);
        $this->assertSame('2026-04-15', $prefill['start_date']);
        $this->assertSame('+255700000000', $prefill['phone']);
        $this->assertSame('guest@example.com', $prefill['email']);
        $this->assertSame('Emanuel', $prefill['fname']);
        $this->assertSame('Sige', $prefill['lname']);
        $this->assertNotEmpty($prefill['note']);
        $this->assertStringContainsString('quote', strtolower($prefill['note']));
        $this->assertSame('Nungwi', $prefill['address']);
        $this->assertSame(7, $prefill['season_id']);
    }

    public function test_links_block_exposes_the_existing_save_booking_endpoint(): void
    {
        $resource = new PublicQuoteResource($this->buildVersionFixture());
        $payload = $resource->additional($this->fakeContext())->toArray(Request::create('/'));

        $this->assertArrayHasKey('links', $payload);
        $this->assertArrayHasKey('submit_booking', $payload['links']);
        $this->assertStringContainsString(
            '/api/save_booking',
            $payload['links']['submit_booking'],
            'The booking-bridge resource MUST point the public site at the existing save_booking endpoint, not a fork.'
        );
    }

    public function test_split_name_handles_blank_and_single_word_names(): void
    {
        $resource = new PublicQuoteResource(new QuotationVersion());
        $this->assertSame(['', ''], $resource->splitName(null));
        $this->assertSame(['', ''], $resource->splitName('   '));
        $this->assertSame(['Madonna', ''], $resource->splitName('Madonna'));
        $this->assertSame(['Emanuel', 'Sige'], $resource->splitName('Emanuel Sige'));
        $this->assertSame(['Mary', 'Anne Doe'], $resource->splitName('Mary Anne Doe'));
    }

    private function buildVersionFixture(): QuotationVersion
    {
        $country = (new Country())->forceFill(['id' => 1, 'name' => 'Tanzania']);

        $tourist = (new Tourist())->forceFill([
            'id' => 100,
            'name' => 'Emanuel Sige',
            'email' => 'guest@example.com',
            'phone' => '+255700000000',
            'gender_id' => 1,
            'country_id' => 1,
            'address' => 'Nungwi',
        ]);
        $tourist->setRelation('country', $country);

        $inquiry = (new Inquiry())->forceFill(['id' => 200, 'tourist_id' => 100]);
        $inquiry->setRelation('tourist', $tourist);

        $quotation = (new Quotation())->forceFill(['id' => 300, 'inquiry_id' => 200]);
        $quotation->setRelation('inquiry', $inquiry);

        $trip = (new Trip())->forceFill(['id' => 400, 'uuid' => 'trip-uuid-abc', 'name' => '9 Days Romantic']);

        $currency = (new Currency())->forceFill(['id' => 1, 'short_name' => 'USD']);

        $version = (new QuotationVersion())->forceFill([
            'id' => 500,
            'quotation_id' => 300,
            'trip_id' => 400,
            'reference_number' => 'SBS-20260101-V1',
            'title' => '9 Days Romantic',
            'subtitle' => 'Crafted for the Sige family',
            'currency_id' => 1,
            'service_class_id' => 2,
            'season_id' => 7,
            'guest_count' => 2,
            'duration_days' => 9,
            'duration_nights' => 8,
            'start_date' => '2026-04-15',
            'end_date' => '2026-04-23',
            'amount' => 1200.00,
            'vat_amount' => 216.00,
            'total_amount' => 1416.00,
            'hide_price_breakdown' => false,
            'hide_total_price' => false,
            'public_token' => 'public-token-xyz',
            'public_url_enabled' => true,
            'status' => 'draft',
        ]);
        $version->setRelation('quotation', $quotation);
        $version->setRelation('trip', $trip);
        $version->setRelation('currency', $currency);
        $version->setRelation('priceLines', new Collection());
        $version->setRelation('terms', new Collection());
        $version->setRelation('paymentTerms', new Collection());

        return $version;
    }

    private function fakeContext(): array
    {
        return [
            'document_reference' => 'SBS-20260101-V1',
            'agent' => ['name' => 'Nikita Stock', 'email' => 'nikita@serenbluesafaris.com', 'phone' => null],
            'highlights_list' => ['Ngorongoro Crater', 'Central Serengeti'],
            'summary_table' => [],
            'public_itinerary_url' => 'https://www.serenbluesafaris.com/itinerary/public-token-xyz',
            'public_booking_url' => 'https://www.serenbluesafaris.com/itinerary/public-token-xyz/book',
            'public_pdf_url' => 'http://localhost/itinerary/public-token-xyz/download-pdf',
        ];
    }
}
