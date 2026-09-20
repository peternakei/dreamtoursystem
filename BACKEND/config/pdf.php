<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default PDF driver
    |--------------------------------------------------------------------------
    |
    | "browsershot" uses Headless Chrome via Spatie\Browsershot for full visual
    | parity with the digital quote. "dompdf" keeps the legacy renderer.
    |
    | When the driver is "browsershot" and rendering fails (Node/Chrome missing,
    | timeout, etc.) the QuotePdfService automatically falls back to DomPDF so
    | downloads never break.
    |
    */
    'driver' => env('PDF_DRIVER', 'browsershot'),

    'browsershot' => [
        // Absolute paths to Node/NPM binaries. Leave null on Linux to use $PATH.
        // On Windows set explicit paths e.g. C:\Program Files\nodejs\node.exe
        'node_binary' => env('PDF_NODE_BINARY'),
        'npm_binary' => env('PDF_NPM_BINARY'),

        // Path to the Chrome/Chromium executable. Leave null to let Puppeteer
        // pick the bundled Chromium (installed via `npm install puppeteer`).
        'chrome_path' => env('PDF_CHROME_PATH'),

        // include_path needed by the Node helper script. Leave null to use the
        // project root's node_modules; override only for non-standard layouts.
        'include_path' => env('PDF_NODE_INCLUDE_PATH'),

        // Wait strategy: "load" / "domcontentloaded" / "networkidle0" / "networkidle2".
        // "load" is fastest when assets are inlined as file:// URIs (the default
        // path) because there are no network requests to wait on. Switch to
        // "networkidle0" if you ever set PDF_INLINE_LOCAL_ASSETS=false.
        'wait_until' => env('PDF_WAIT_UNTIL', 'load'),

        // Timeout for the headless render in milliseconds.
        'timeout_ms' => (int) env('PDF_TIMEOUT_MS', 60000),

        // When true, asset URLs that point at this app's `public/` directory
        // are rewritten to `file://` URIs before Chromium opens the HTML.
        // Required when running under `php artisan serve` (single-process →
        // would deadlock) and faster everywhere because no HTTP round-trip.
        'inline_local_assets' => env('PDF_INLINE_LOCAL_ASSETS', true),

        // Page format / margins (mm).
        'format' => env('PDF_FORMAT', 'A4'),
        'margin_top' => (float) env('PDF_MARGIN_TOP', 8),
        'margin_right' => (float) env('PDF_MARGIN_RIGHT', 8),
        'margin_bottom' => (float) env('PDF_MARGIN_BOTTOM', 10),
        'margin_left' => (float) env('PDF_MARGIN_LEFT', 8),
    ],
];
