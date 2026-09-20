<?php

namespace App\Project\Modules\System\Quotations;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Quotations\Services\QuotePdfService;
use App\Project\Modules\System\Quotations\Services\QuoteShareDraftService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuotationPdfController extends Controller
{
    public function generate(
        QuotePdfService $quotePdfService,
        QuoteShareDraftService $quoteShareDraftService,
        string $id
    ) {
        $version = QuotationVersion::where('uuid', $id)->firstOrFail();
        $quotePdfService->generate($version);

        $shareDraft = $quoteShareDraftService->buildDraftForVersion($version->fresh());

        return redirect()
            ->route('quotation_versions.preview', $id)
            ->with('success', 'Quote PDF generated successfully. Use the share panel below to email or WhatsApp your guest.')
            ->with('quote_share', $shareDraft);
    }

    public function shareLink(QuoteShareDraftService $quoteShareDraftService, string $id)
    {
        $version = QuotationVersion::where('uuid', $id)->firstOrFail();

        // Make sure the public proposal page is reachable before drafting the share message.
        $updates = [];
        if (!$version->public_token) {
            $updates['public_token'] = Str::random(48);
        }
        if (!$version->public_url_enabled) {
            $updates['public_url_enabled'] = true;
        }
        if (!empty($updates)) {
            $version->update($updates);
        }

        $shareDraft = $quoteShareDraftService->buildDraftForVersion($version->fresh());

        return redirect()
            ->route('quotation_versions.preview', $id)
            ->with('success', 'Digital share link is ready. Use the panel below to copy or send it.')
            ->with('quote_share', $shareDraft);
    }

    public function download(QuotePdfService $quotePdfService, string $id)
    {
        $version = QuotationVersion::where('uuid', $id)->firstOrFail();

        if (!$version->pdf_path) {
            $quotePdfService->generate($version);
            $version->refresh();
        }

        return Storage::disk('public')->download($version->pdf_path, $quotePdfService->downloadName($version) . '.pdf');
    }

    /**
     * Render the same HTML that Browsershot turns into a PDF, served back to
     * the agent as a normal web page. Useful for debugging visual parity with
     * the digital quote without round-tripping through Chromium.
     */
    public function printPreview(QuoteBuilderService $quoteBuilderService, string $id)
    {
        $version = QuotationVersion::where('uuid', $id)->firstOrFail();

        // Match the render path the PDF service uses: screen-mode asset URLs
        // so the digital styling and images load identically.
        $data = $quoteBuilderService->buildDocumentData($version, false);

        return view('web.system.quotation.pdf.print', $data);
    }
}
