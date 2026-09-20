<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\System\Quotations\Services\QuotePdfService;
use App\Project\Modules\System\Quotations\Services\QuoteAssetService;

use App\Project\Modules\System\Quotations\QuotationVersion;

class QuoteShareDraftService
{
    public function __construct(
        protected QuoteAssetService $assetService,
        protected QuotePdfService $pdfService,
    ) {
    }

    /**
     * Human-readable drafts for email and WhatsApp after a PDF is ready.
     *
     * @return array{
     *     guest_name: string,
     *     guest_email: string,
     *     email_subject: string,
     *     email_body: string,
     *     whatsapp_message: string,
     *     public_proposal_url: ?string,
     *     public_pdf_url: ?string,
     *     staff_pdf_download_url: string,
     *     public_link_enabled: bool,
     *     reference_number: string,
     *     proposal_title: string
     * }
     */
    public function buildDraftForVersion(QuotationVersion $version): array
    {
        $version->loadMissing('quotation.inquiry.tourist');

        $tourist = $version->quotation?->inquiry?->tourist;
        $guestName = optional($tourist)->name ?: 'Guest';
        $guestEmail = optional($tourist)->email ?: '';

        $company = $this->assetService->companyName();
        $ref = $version->reference_number;
        $title = $version->title ?: 'Safari proposal';

        $publicEnabled = (bool) $version->public_url_enabled && filled($version->public_token);
        $shareSlug = $this->pdfService->downloadName($version);
        $publicProposalUrl = $publicEnabled
            ? $this->assetService->publicItineraryUrl($version->public_token, 'guest_share', $shareSlug)
            : null;
        // Always expose the public PDF URL when the public link is enabled —
        // the public download route lazy-generates the PDF on first hit, so we
        // do not need pdf_path to already exist before sharing the link.
        $publicPdfUrl = $publicEnabled
            ? $this->assetService->publicPdfUrl($version->public_token)
            : null;

        $staffPdfUrl = route('quotation_versions.pdf.download', $version->uuid);

        $subject = sprintf('%s — %s (%s)', $company, $title, $ref);

        $bodyLines = [
            sprintf('Hi %s,', $guestName),
            '',
            sprintf('Your %s proposal "%s" is ready (reference %s).', $company, $title, $ref),
            '',
        ];

        if ($publicProposalUrl) {
            $bodyLines[] = 'View the interactive proposal (and download your PDF):';
            $bodyLines[] = $publicProposalUrl;
            if ($publicPdfUrl) {
                $bodyLines[] = '';
                $bodyLines[] = 'Direct PDF download:';
                $bodyLines[] = $publicPdfUrl;
            }
            $bodyLines[] = '';
            $bodyLines[] = 'If anything needs adjusting, reply to this email or use the options on the proposal page.';
        } else {
            $bodyLines[] = 'Your consultant will send a secure viewing link shortly, or you can ask us to enable the guest link from your proposal workspace.';
            $bodyLines[] = '';
            $bodyLines[] = 'Internal PDF (team, sign-in required):';
            $bodyLines[] = $staffPdfUrl;
        }

        $bodyLines[] = '';
        $bodyLines[] = 'Kind regards,';
        $bodyLines[] = $company;

        $emailBody = implode("\n", $bodyLines);

        $waLines = [
            sprintf('Hi %s — your %s proposal "%s" (%s) is ready.', $guestName, $company, $title, $ref),
        ];
        if ($publicProposalUrl) {
            $waLines[] = $publicProposalUrl;
        } else {
            $waLines[] = 'We will send your secure link shortly.';
        }

        $whatsappMessage = implode("\n", $waLines);

        return [
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'email_subject' => $subject,
            'email_body' => $emailBody,
            'whatsapp_message' => $whatsappMessage,
            'public_proposal_url' => $publicProposalUrl,
            'public_pdf_url' => $publicPdfUrl,
            'staff_pdf_download_url' => $staffPdfUrl,
            'public_link_enabled' => $publicEnabled,
            'reference_number' => $ref,
            'proposal_title' => $title,
        ];
    }
}
