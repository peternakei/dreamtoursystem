<?php

namespace Tests\Unit\Quotation;

use App\Project\Modules\System\Quotations\Services\QuoteAssetService;
use Tests\TestCase;

class QuoteAssetServiceTest extends TestCase
{
    public function test_company_name_falls_back_when_config_default_is_unset(): void
    {
        $service = new QuoteAssetService();

        config()->set('app.name', '');
        $this->assertSame('Dream Travel and Tours', $service->companyName());

        config()->set('app.name', 'SBS ');
        $this->assertSame('Dream Travel and Tours', $service->companyName());

        config()->set('app.name', 'Laravel');
        $this->assertSame('Dream Travel and Tours', $service->companyName());

        config()->set('app.name', 'Acme Travel');
        $this->assertSame('Acme Travel', $service->companyName());
    }

    public function test_public_site_url_strips_trailing_slash(): void
    {
        $service = new QuoteAssetService();

        config()->set('app.public_site_url', 'https://example.com/');
        $this->assertSame('https://example.com', $service->publicSiteUrl());

        config()->set('app.public_site_url', 'https://example.com');
        $this->assertSame('https://example.com', $service->publicSiteUrl());
    }

    public function test_public_itinerary_url_returns_null_for_blank_token(): void
    {
        $service = new QuoteAssetService();

        $this->assertNull($service->publicItineraryUrl(null));
        $this->assertNull($service->publicItineraryUrl(''));
        $this->assertNull($service->publicBookingUrl(null));
        $this->assertNull($service->publicPdfUrl(null));
    }

    public function test_public_itinerary_url_includes_token_and_utm_params(): void
    {
        config()->set('app.public_site_url', 'https://example.com');
        $service = new QuoteAssetService();

        $url = $service->publicItineraryUrl('abc123');

        $this->assertStringContainsString(route('public.itinerary.show', ['token' => 'abc123']), $url);
        $this->assertStringContainsString('utm_source=', $url);
        $this->assertStringContainsString('utm_campaign=safari_quote', $url);
    }

    public function test_public_booking_url_points_at_marketing_site_not_dead_path(): void
    {
        config()->set('app.public_site_url', 'https://example.com');
        $service = new QuoteAssetService();

        $url = $service->publicBookingUrl('abc123');

        // Phase 1 fix: was '/booking/quote/' which had no backend handler.
        // Now '/itinerary/{token}/book' on the marketing site, which the
        // public site routes to its booking checkout.
        $this->assertStringContainsString('/itinerary/abc123/book', $url);
    }
}
