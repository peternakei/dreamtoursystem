<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;

use App\Project\Modules\System\Quotations\QuotationVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Browsershot\Browsershot;
use Throwable;

class QuotePdfService
{
    public function __construct(protected QuoteBuilderService $quoteBuilderService)
    {
    }

    public function generate(QuotationVersion $version): string
    {
        $this->ensurePublicShareToken($version);

        // PDF rendering shells out to Chromium and can exceed the default
        // PHP request budget (60s on most setups). Lift the time limit so the
        // web request matches what the CLI generator already supports.
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $fileName = 'quotations/' . $this->downloadName($version) . '.pdf';
        $driver = config('pdf.driver', 'browsershot');

        if ($driver === 'browsershot') {
            try {
                $this->generateWithBrowsershot($version, $fileName);
                $version->update(['pdf_path' => $fileName]);

                return $fileName;
            } catch (Throwable $e) {
                Log::warning('Browsershot PDF render failed, falling back to DomPDF.', [
                    'version_uuid' => $version->uuid,
                    'error' => $e->getMessage(),
                ]);
                // Fall through to DomPDF.
            }
        }

        $this->generateWithDompdf($version, $fileName);
        $version->update(['pdf_path' => $fileName]);

        return $fileName;
    }

    public function downloadName(QuotationVersion $version): string
    {
        return $this->quoteBuilderService->buildShareSlug($version);
    }

    /**
     * Render the digital-quote-equivalent print template through Headless
     * Chrome so the PDF visually matches what guests see in the public link.
     */
    protected function generateWithBrowsershot(QuotationVersion $version, string $fileName): void
    {
        // Use screen-mode asset URLs so the rendered HTML matches the digital
        // page; we then rewrite same-host URLs to file:// so Chromium does
        // not need to round-trip back through PHP for assets. That avoids a
        // deadlock under the single-process `php artisan serve` runtime and
        // shaves 1-2s per render in production by skipping HTTP entirely.
        $data = $this->quoteBuilderService->buildDocumentData($version, false);
        $html = view('web.system.quotation.pdf.print', $data)->render();

        $useFileUris = (bool) config('pdf.browsershot.inline_local_assets', true);
        if ($useFileUris) {
            $html = $this->inlineLocalAssetsAsFileUris($html);
        }

        $absolutePath = Storage::disk('public')->path($fileName);
        $this->ensureDirectoryExists($absolutePath);

        // Browsershot::html() rejects any string containing `file://` for
        // safety, but Chromium itself happily loads file:// images when the
        // parent page is also local. Drop the HTML on disk and point
        // Browsershot at it via htmlFromFilePath(), which skips the check.
        $tempHtmlPath = $useFileUris ? $this->writeTemporaryHtml($html) : null;

        $timeoutSeconds = (int) ceil(((int) config('pdf.browsershot.timeout_ms', 60000)) / 1000);

        $shot = $tempHtmlPath
            ? Browsershot::htmlFromFilePath($tempHtmlPath)
            : Browsershot::html($html);

        $shot
            ->showBackground()
            ->emulateMedia('screen')
            ->format(config('pdf.browsershot.format', 'A4'))
            ->margins(
                (float) config('pdf.browsershot.margin_top', 8),
                (float) config('pdf.browsershot.margin_right', 8),
                (float) config('pdf.browsershot.margin_bottom', 10),
                (float) config('pdf.browsershot.margin_left', 8)
            )
            ->timeout($timeoutSeconds)
            // Chromium stability flags — same set Spatie recommends for
            // headless rendering on hosts without a sandbox.
            ->noSandbox()
            ->addChromiumArguments([
                'disable-dev-shm-usage',
                'disable-gpu',
                'hide-scrollbars',
            ]);

        // Use the configured wait strategy so admins can switch to a more
        // lenient mode if Chromium ever stalls on slow asset hosts.
        $waitUntil = (string) config('pdf.browsershot.wait_until', 'networkidle0');
        if ($waitUntil === 'networkidle0' || $waitUntil === 'networkidle2') {
            $shot->waitUntilNetworkIdle($waitUntil === 'networkidle0');
        } else {
            $shot->setOption('waitUntil', $waitUntil);
        }

        // Small post-idle settle so Leaflet finishes painting tiles after the
        // last network response — networkidle0 fires when the socket queue
        // drains, not when the canvas is fully repainted.
        $tilePaintDelayMs = (int) config('pdf.browsershot.post_idle_delay_ms', 600);
        if ($tilePaintDelayMs > 0) {
            $shot->setDelay($tilePaintDelayMs);
        }

        if ($nodeBinary = config('pdf.browsershot.node_binary')) {
            $shot->setNodeBinary($nodeBinary);
        }
        if ($npmBinary = config('pdf.browsershot.npm_binary')) {
            $shot->setNpmBinary($npmBinary);
        }
        if ($chromePath = config('pdf.browsershot.chrome_path')) {
            $shot->setChromePath($chromePath);
        }
        if ($includePath = config('pdf.browsershot.include_path')) {
            $shot->setIncludePath($includePath);
        }

        try {
            $shot->save($absolutePath);
        } finally {
            if ($tempHtmlPath && is_file($tempHtmlPath)) {
                @unlink($tempHtmlPath);
            }
        }
    }

    /**
     * Persist the rendered HTML so Browsershot can open it as `file://...`
     * — the only way to bypass setHtml()'s rejection of file:// references
     * inside the HTML body.
     */
    protected function writeTemporaryHtml(string $html): string
    {
        $dir = storage_path('app/tmp/pdf');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $path = $dir . DIRECTORY_SEPARATOR . 'pdf-' . bin2hex(random_bytes(8)) . '.html';
        file_put_contents($path, $html);

        return $path;
    }

    /**
     * Legacy DomPDF render. Kept as an automatic fallback for hosts where
     * Headless Chrome / Node is unavailable so guests never see a broken
     * download link.
     */
    protected function generateWithDompdf(QuotationVersion $version, string $fileName): void
    {
        // DomPDF's PNG path needs the GD extension; uploaded destination
        // images are commonly PNG so a GD-less PHP build will explode mid-
        // render. Surface that early instead of leaking a vendor stack trace.
        if (!function_exists('imagecreatefrompng')) {
            throw new RuntimeException(
                'PDF generation requires either Headless Chrome (Browsershot) '
                . 'or the PHP GD extension for the DomPDF fallback. Install '
                . 'puppeteer (`npm install puppeteer`) or enable php_gd in '
                . 'php.ini, then retry.'
            );
        }

        $data = $this->quoteBuilderService->buildDocumentData($version, true);

        $pdf = Pdf::loadView('web.system.quotation.pdf.default', $data)
            ->setPaper('a4');

        Storage::disk('public')->put($fileName, $pdf->output());
    }

    /**
     * Replace `http(s)://{this-host}/path` references with `file:///{public}/path`
     * for any asset that maps onto a real file inside `public/`. Other URLs
     * (CDNs, third-party images) are left alone so they keep working in PDF.
     */
    protected function inlineLocalAssetsAsFileUris(string $html): string
    {
        $publicPath = str_replace('\\', '/', rtrim(public_path(), '/\\'));
        $fileUrlPrefix = 'file:///' . ltrim($publicPath, '/') . '/';

        $candidates = [];

        if ($appUrl = rtrim((string) config('app.url'), '/')) {
            $candidates[] = $appUrl . '/';
        }

        if ($asset = URL::asset('/')) {
            $candidates[] = $asset;
        }

        if ($request = request()) {
            $candidates[] = rtrim($request->getSchemeAndHttpHost(), '/') . '/';
        }

        // De-dupe and run the longest prefix first so http://host/sub/ wins
        // over http://host/ when both match.
        $candidates = array_values(array_unique(array_filter($candidates)));
        usort($candidates, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($candidates as $prefix) {
            $html = str_replace($prefix, $fileUrlPrefix, $html);
        }

        return $html;
    }

    protected function ensurePublicShareToken(QuotationVersion $version): void
    {
        // The recipient PDF always carries a working "View online" / "Accept"
        // link, so make sure the public-itinerary token exists before render.
        $publicUpdates = [];
        if (!$version->public_token) {
            $publicUpdates['public_token'] = Str::random(48);
        }
        if (!$version->public_url_enabled) {
            $publicUpdates['public_url_enabled'] = true;
        }
        if (!empty($publicUpdates)) {
            $version->update($publicUpdates);
            $version->refresh();
        }
    }

    protected function ensureDirectoryExists(string $absolutePath): void
    {
        $dir = dirname($absolutePath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }
}
