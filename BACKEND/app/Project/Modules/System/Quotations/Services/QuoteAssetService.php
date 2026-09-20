<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Users\SystemUser;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Vehicles\Vehicle;

class QuoteAssetService
{
    public function branding(): array
    {
        $systemUser = SystemUser::query()->first();
        $companyName = $this->companyName();

        return [
            'company_name' => $companyName,
            'tagline' => 'Crafting unforgettable Tanzania safaris with heart, detail, and local expertise.',
            'logo_url' => $this->firstExistingAssetUrl([
                'images/dream-logo.png',
                'assets/images/logo.png',
                'images/logo.png',
                'assets/images/logo.jpg',
                'images/logo.jpg',
                'assets/images/logo.jpeg',
                'images/logo.jpeg',
            ]),
            'logo_pdf_path' => $this->firstDompdfEmbeddablePublicPath(['images/dream-logo.png', 'images/dream-logo-print.jpeg']),
            'email' => config('mail.from.address') ?: $systemUser?->email,
            'phone' => $systemUser?->phone,
            'address' => $systemUser?->address,
            'website_url' => $this->publicSiteUrl(),
            'powered_by_label' => 'Powered by Dream Travel and Tours',
            'company_profile' => sprintf(
                '%s crafts immersive safari proposals using destination imagery, curated routes, and clear commercial detail so every quote feels ready for a guest conversation from the first draft.',
                $companyName
            ),
            'palette' => [
                'forest' => '#288479',
                'sand' => '#F7EFDF',
                'ink' => '#101010',
                'sun' => '#FFA319',
                'mist' => '#F7EFDF',
            ],
        ];
    }

    /**
     * Resolved company name with a sane fallback so we never render the literal config default.
     */
    public function companyName(): string
    {
        $name = trim((string) config('app.name'));

        if ($name === '' || strcasecmp($name, 'laravel') === 0 || str_starts_with($name, 'SBS')) {
            return 'Dream Travel and Tours';
        }

        return $name;
    }

    /**
     * Single source of truth for the public marketing site root URL.
     */
    public function publicSiteUrl(): string
    {
        return rtrim((string) config('app.public_site_url', 'https://www.serenbluesafaris.com'), '/');
    }

    /**
     * Guest-facing digital itinerary URL — served by this backoffice app
     * (route public.itinerary.show), not the public marketing site.
     */
    public function publicItineraryUrl(?string $token, string $source = 'digital_itinerary', ?string $slug = null): ?string
    {
        if (!filled($token)) {
            return null;
        }

        $params = ['token' => $token];
        if (filled($slug)) {
            $params['slug'] = $slug;
        }

        return route('public.itinerary.show', $params)
            . '?utm_source=' . $source . '&utm_medium=web&utm_campaign=safari_quote';
    }

    /**
     * Guest-facing booking entry URL — dedicated SafariOffice-style "Confirm Your Booking"
     * page (route public.itinerary.book.confirm). Booking is submitted via
     * public.itinerary.book.
     */
    public function publicBookingUrl(?string $token, string $source = 'quote_pdf', ?string $slug = null): ?string
    {
        if (!filled($token)) {
            return null;
        }

        return route('public.itinerary.book.confirm', ['token' => $token])
            . '?utm_source=' . $source . '&utm_medium=web&utm_campaign=safari_quote';
    }

    /**
     * Backend-served PDF download URL (the PDF lives on this Laravel app, not the marketing site).
     */
    public function publicPdfUrl(?string $token): ?string
    {
        if (!filled($token)) {
            return null;
        }

        return route('public.itinerary.download', $token);
    }

    /**
     * Static company-wide content rendered on the About / Colofon pages.
     */
    public function companyContent(): array
    {
        return [
            'mission' => (string) config('company.mission'),
            'vision' => (string) config('company.vision'),
            'tagline' => (string) config('company.tagline'),
            'address' => config('company.address'),
            'country' => config('company.country'),
            'email' => config('company.email'),
            'phone' => config('company.phone'),
        ];
    }

    /**
     * Vehicles surfaced on the proposal "Vehicles" page. Reads from the
     * Vehicle model when available; falls back to config/company.php so
     * pre-Library quotes still render. Image paths are resolved per-mode
     * (asset URL for screen, absolute path for DomPDF).
     */
    public function vehicles(bool $forPdf = false): array
    {
        $dbVehicles = Vehicle::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($dbVehicles->isNotEmpty()) {
            $fallback = $this->defaultHeroImage($forPdf);

            return $dbVehicles->map(function (Vehicle $vehicle) use ($forPdf, $fallback) {
                $cover = $vehicle->primaryCover();

                return [
                    'name' => $vehicle->name,
                    'description' => $vehicle->description,
                    'capacity' => $vehicle->capacity,
                    'image_resolved' => $this->attachmentSource($cover, $forPdf) ?: $fallback,
                ];
            })->all();
        }

        return collect((array) config('company.vehicles', []))
            ->map(function (array $vehicle) use ($forPdf) {
                $relative = $vehicle['image'] ?? null;
                $vehicle['image_resolved'] = $this->resolveStaticAsset($relative, $forPdf);

                return $vehicle;
            })
            ->all();
    }

    public function colofon(): array
    {
        return [
            'quote' => (string) config('company.colofon.quote'),
            'quote_author' => (string) config('company.colofon.quote_author'),
            'copyright_text' => (string) config('company.colofon.copyright_text'),
            'copyright_images' => (string) config('company.colofon.copyright_images'),
        ];
    }

    /**
     * Resolve the agent identity for a quotation version. Reads the
     * version's `created_by` user and joins through to its system_users
     * profile row (`User::userProfile()` is auth-bound, so we must not
     * use it here).
     */
    public function agentForVersion(?QuotationVersion $version): array
    {
        $branding = $this->branding();
        $fallback = [
            'name' => $branding['company_name'],
            'email' => $branding['email'],
            'phone' => $branding['phone'],
        ];

        if (!$version || !$version->created_by) {
            return $fallback;
        }

        $user = User::query()->find($version->created_by);
        if (!$user) {
            return $fallback;
        }

        if ($user->profile === 'SystemUser' && $user->profile_id) {
            $profile = SystemUser::query()->find($user->profile_id);
            if ($profile) {
                return [
                    'name' => $profile->name ?: $fallback['name'],
                    'email' => $profile->email ?: $fallback['email'],
                    'phone' => $profile->phone ?: $fallback['phone'],
                ];
            }
        }

        return $fallback;
    }

    /**
     * Resolve a static public/-rooted asset path to either an asset() URL
     * (screen) or an absolute filesystem path (DomPDF). Returns null when
     * the file isn't present.
     */
    public function resolveStaticAsset(?string $relativePath, bool $forPdf = false): ?string
    {
        if (!filled($relativePath)) {
            return null;
        }

        $absolute = public_path($relativePath);
        if (!is_file($absolute)) {
            return null;
        }

        if (!$forPdf) {
            return asset($relativePath);
        }

        return $this->absolutePathForDompdf($absolute);
    }

    public function tripHero(?Trip $trip, bool $forPdf = false): ?string
    {
        $attachment = $trip?->banners()->latest()->first();

        return $this->attachmentSource($attachment, $forPdf);
    }

    /**
     * Last-resort fallback hero used when a quotation has no trip banner,
     * cover upload, or destination imagery. Configurable via
     * `quote.default_hero_image` (path relative to public/) — falls back to
     * the first present option from a curated list so freshly-cloned dev
     * environments still render a real photo instead of a solid block of
     * brand color.
     */
    public function defaultHeroImage(bool $forPdf = false): ?string
    {
        $candidates = [
            (string) config('quote.default_hero_image'),
            'storage/uploads/1648652351095700.jpeg',
            'storage/uploads/1648689982460500_0.jpeg',
            'storage/uploads/1648774888720200.jpeg',
            'assets/images/background.jpg',
        ];

        foreach ($candidates as $relative) {
            if (!filled($relative)) {
                continue;
            }
            $resolved = $this->resolveStaticAsset($relative, $forPdf);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    public function destinationImage(?Destination $destination, bool $forPdf = false): ?string
    {
        $attachment = $destination?->images()->latest()->first();

        return $this->attachmentSource($attachment, $forPdf);
    }

    public function attachmentSource(?Attachment $attachment, bool $forPdf = false): ?string
    {
        if (!$attachment?->name) {
            return null;
        }

        $relativePath = 'storage/uploads/' . $attachment->name;

        if (!$forPdf) {
            return asset($relativePath);
        }

        $absolute = $this->publicPath($relativePath);

        return $this->absolutePathForDompdf($absolute);
    }

    protected function firstExistingAssetUrl(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

    protected function firstExistingPublicPath(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (file_exists(public_path($path))) {
                return public_path($path);
            }
        }

        return null;
    }

    protected function publicPath(string $relativePath): ?string
    {
        $absolute = public_path($relativePath);

        return file_exists($absolute) ? $absolute : null;
    }

    /**
     * Dompdf's CPDF backend embeds JPEG files without GD; PNG uses imagecreatefrompng() when present.
     * Prefer the same capability check Dompdf uses — extension_loaded('gd') alone can be misleading.
     */
    protected function dompdfSupportsPngRaster(): bool
    {
        return function_exists('imagecreatefrompng');
    }

    /**
     * Return an absolute filesystem path Dompdf can embed, or null when it would require missing PNG support.
     */
    protected function absolutePathForDompdf(?string $absolutePath): ?string
    {
        if (!$absolutePath || !is_file($absolutePath)) {
            return null;
        }

        if ($this->dompdfSupportsPngRaster()) {
            return $this->normalizeFilesystemPathForDompdf($absolutePath);
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg'], true)) {
            return null;
        }

        $mime = $this->guessMimeType($absolutePath);
        if ($mime !== null) {
            $base = strtolower(strtok($mime, ';') ?: '');
            if (!in_array($base, ['image/jpeg', 'image/jpg'], true)) {
                return null;
            }
        }

        return $this->normalizeFilesystemPathForDompdf($absolutePath);
    }

    /**
     * Dompdf/CSS url() parsing can mis-handle Windows backslashes in local paths.
     */
    protected function normalizeFilesystemPathForDompdf(string $absolutePath): string
    {
        return str_replace('\\', '/', $absolutePath);
    }

    protected function guessMimeType(string $absolutePath): ?string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mime = finfo_file($finfo, $absolutePath);
                finfo_close($finfo);
                if (is_string($mime) && $mime !== '') {
                    return $mime;
                }
            }
        }

        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($absolutePath);

            return is_string($mime) && $mime !== '' ? $mime : null;
        }

        return null;
    }

    protected function firstDompdfEmbeddablePublicPath(array $paths): ?string
    {
        foreach ($paths as $path) {
            $absolute = public_path($path);
            if (!is_file($absolute)) {
                continue;
            }

            $safe = $this->absolutePathForDompdf($absolute);
            if ($safe !== null) {
                return $safe;
            }
        }

        return null;
    }
}
