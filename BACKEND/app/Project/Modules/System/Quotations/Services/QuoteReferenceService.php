<?php

namespace App\Project\Modules\System\Quotations\Services;

use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\Quotation;

class QuoteReferenceService
{
    public function nextRequestReference(): string
    {
        $year = now()->format('Y');
        $max = Inquiry::query()
            ->where('request_reference', 'like', $year . '-%')
            ->pluck('request_reference')
            ->map(fn($reference) => (int) preg_replace('/^' . preg_quote($year, '/') . '-/', '', (string) $reference))
            ->max() ?? 0;

        return sprintf('%s-%04d', $year, $max + 1);
    }

    public function nextQuotationNumber(): string
    {
        $base = 'SBS-' . now()->format('Ymd-Hi');
        $candidate = $base;
        $suffix = 2;

        while (Quotation::query()->where('quotation_number', $candidate)->exists()) {
            $candidate = sprintf('%s-%02d', $base, $suffix);
            $suffix++;
        }

        return $candidate;
    }

    public function versionReference(string $quotationNumber, int $versionNumber): string
    {
        return sprintf('%s-V%d', $quotationNumber, $versionNumber);
    }
}
