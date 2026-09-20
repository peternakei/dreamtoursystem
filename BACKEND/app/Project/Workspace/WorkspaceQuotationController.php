<?php

namespace App\Project\Workspace;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Quotations\QuotationController;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Quotations\QuotationVersionController;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Quotations\Services\QuotePdfService;
use App\Project\Modules\System\Quotations\Services\QuotePricingService;
use App\Project\Modules\System\Quotations\Services\QuoteShareDraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkspaceQuotationController extends Controller
{
    public function show(string $id)
    {
        $quotation = Quotation::with(['tourist.country', 'inquiry.serviceDetails', 'createdFromTrip', 'status', 'currency', 'currentVersion.currency', 'versions.currency'])
            ->where('uuid', $id)->firstOrFail();
        $quotation->currentVersion?->loadCount(['days', 'priceLines', 'terms', 'paymentTerms']);

        return response()->json(['quotation' => $quotation]);
    }

    private function version(string $id): QuotationVersion
    {
        return QuotationVersion::with('quotation.inquiry.serviceDetails')->where('uuid', $id)->firstOrFail();
    }

    private function tripOnly(QuotationVersion $version): void
    {
        if ($version->quotation?->inquiry?->serviceDetails) {
            throw ValidationException::withMessages(['quotation' => 'Manage this service quotation through its service request, where supply verification and agreed-price rules apply.']);
        }
    }

    public function builder(string $id)
    {
        $this->tripOnly($this->version($id));
        // Reuse the existing builder catalogs without rendering its Blade layout.
        $data = app(QuotationVersionController::class)->edit($id)->getData();

        return response()->json($data);
    }

    public function update(Request $request, string $id, QuoteBuilderService $builder)
    {
        $version = $this->version($id);
        $this->tripOnly($version);
        if ($request->has('payload')) {
            $request->validate(['payload' => 'required|json']);
            $decoded = json_decode($request->input('payload'), true);
            abort_unless(is_array($decoded), 422, 'Invalid quotation payload.');
            $request->merge($decoded);
        }
        $rules = [
            'title' => 'required|string|max:255', 'subtitle' => 'nullable|string|max:255',
            'currency_id' => 'required|integer|exists:currencies,id', 'service_class_id' => 'nullable|integer|exists:service_classes,id',
            'start_date' => 'nullable|date', 'end_date' => 'nullable|date', 'guest_count' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|max:5120', 'days' => 'required|array|min:1',
            'days.*.uuid' => 'nullable|uuid|distinct', 'days.*.day_number' => 'required|integer|min:1|distinct',
            'days.*.travel_date' => 'nullable|date', 'days.*.trip_day_id' => 'nullable|integer|exists:trip_days,id',
            'days.*.destination_id' => 'nullable|integer|exists:destinations,id', 'days.*.accommodation_id' => 'nullable|integer|exists:accommodations,id',
            'days.*.nights' => 'required|integer|min:0', 'days.*.activity_ids' => 'nullable|array', 'days.*.activity_ids.*' => 'integer|exists:activities,id',
            'price_lines' => 'present|array', 'price_lines.*.description' => 'required|string',
            'price_lines.*.quantity' => 'required|integer|min:1', 'price_lines.*.unit_price' => 'required|numeric|min:0',
            'price_lines.*.total_price' => 'required|numeric|min:0', 'price_lines.*.currency_id' => 'required|integer|exists:currencies,id',
            'price_lines.*.source_type' => 'nullable|string', 'price_lines.*.source_id' => 'nullable|integer', 'price_lines.*.traveler_type' => 'nullable|string',
            'included_terms' => 'present|array', 'excluded_terms' => 'present|array', 'payment_terms' => 'present|array',
        ];
        foreach (['introduction', 'highlights', 'agent_intro_letter', 'company_profile', 'internal_notes'] as $key) {
            $rules[$key] = 'nullable|string';
        }
        foreach (['title', 'description', 'accommodation_name', 'accommodation_notes', 'stay_type'] as $key) {
            $rules['days.*.'.$key] = 'nullable|string';
        }
        foreach (['breakfast', 'lunch', 'dinner'] as $key) {
            $rules['days.*.'.$key] = 'required|boolean';
        }
        foreach (['included_terms', 'excluded_terms', 'payment_terms'] as $group) {
            $rules[$group.'.*.title'] = 'nullable|string';
            $rules[$group.'.*.description'] = 'required|string';
            $rules[$group.'.*.is_visible'] = 'required|boolean';
        }
        foreach (['is_visible', 'is_optional'] as $key) {
            $rules['price_lines.*.'.$key] = 'required|boolean';
        }
        foreach (['hide_price_breakdown', 'hide_total_price', 'hide_terms', 'hide_payment_terms', 'public_url_enabled', 'vat_enabled', 'remove_cover_image'] as $key) {
            $rules[$key] = 'required|boolean';
        }
        $data = $request->validate($rules);
        if (! collect($data['days'])->contains(fn ($day) => ! empty($day['destination_id']))) {
            throw ValidationException::withMessages(['days' => 'At least one day must have a destination selected before this quote can be saved.']);
        }
        $data['cover_image'] = $request->file('cover_image');
        // Existing historical free-text activities have no library ID. Retain
        // those snapshots when their day is retained, rather than losing them.
        $oldDays = $version->days()->with('activities')->get()->keyBy('uuid');
        foreach ($data['days'] as $day) {
            if (! empty($day['uuid']) && ! $oldDays->has($day['uuid'])) {
                throw ValidationException::withMessages(['days' => 'A day does not belong to this version.']);
            }
        }
        DB::transaction(function () use ($data, $oldDays, $builder, $version, $request) {
            $saved = $builder->syncVersionContent($version, $data, $request->user()->id);
            $ordered = collect($data['days'])->sortBy('day_number')->values();
            foreach ($saved->days as $index => $day) {
                $source = $oldDays->get($ordered[$index]['uuid'] ?? '');
                foreach ($source?->activities->whereNull('activity_id') ?? [] as $activity) {
                    $day->activities()->create($activity->only(['trip_day_activity_id', 'title', 'description', 'is_optional', 'price', 'currency_id', 'sort_order']) + ['created_by' => $request->user()->id]);
                }
            }
            $saved->update(['pdf_path' => null]);
        });

        return response()->json(['status' => true, 'message' => 'Quotation saved', 'quotation_uuid' => $version->quotation->uuid]);
    }

    public function duplicate(Request $request, string $id, QuoteBuilderService $builder)
    {
        $quotation = Quotation::with('currentVersion')->where('uuid', $id)->firstOrFail();
        abort_unless($quotation->currentVersion, 422, 'This quotation has no version to duplicate.');
        $this->tripOnly($quotation->currentVersion);
        $source = $quotation->currentVersion->load('days.activities');
        $version = DB::transaction(function () use ($quotation, $source, $builder, $request) {
            $version = $builder->duplicateVersion($quotation, $request->user()->id);
            $version->update($source->only(['start_date', 'end_date', 'season_id', 'guest_count', 'highlights', 'agent_intro_letter', 'vat_enabled', 'hide_price_breakdown', 'hide_total_price', 'hide_terms', 'hide_payment_terms']));
            if ($source->cover_image_path && Storage::disk('public')->exists($source->cover_image_path)) {
                $cover = 'quotation-covers/'.Str::uuid().'.'.pathinfo($source->cover_image_path, PATHINFO_EXTENSION);
                if (! Storage::disk('public')->copy($source->cover_image_path, $cover)) {
                    throw new \RuntimeException('Could not copy the quotation cover.');
                }
                $version->update(['cover_image_path' => $cover]);
            }
            foreach ($version->days as $index => $day) {
                // The older duplicate engine does not carry library activity IDs.
                $day->activities()->delete();
                foreach ($source->days[$index]->activities ?? [] as $activity) {
                    $day->activities()->create($activity->only(['trip_day_activity_id', 'activity_id', 'title', 'description', 'is_optional', 'price', 'currency_id', 'sort_order']) + ['created_by' => $request->user()->id]);
                }
            }
            app(QuotePricingService::class)->syncQuotationTotals($version->fresh('quotation'));

            return $version;
        });

        return response()->json(['status' => true, 'version_uuid' => $version->uuid]);
    }

    public function preview(string $id, QuoteShareDraftService $share)
    {
        $version = $this->version($id);
        $version->load(['currency', 'quotation.tourist.country']);

        return response()->json(['version' => $version, 'share' => $share->buildDraftForVersion($version)]);
    }

    public function share(string $id, QuoteShareDraftService $share)
    {
        $version = $this->version($id);
        $version->update(['public_token' => $version->public_token ?: Str::random(48), 'public_url_enabled' => true]);

        return response()->json(['status' => true, 'share' => $share->buildDraftForVersion($version->fresh())]);
    }

    public function pdf(string $id, QuotePdfService $pdf, QuoteShareDraftService $share)
    {
        $version = $this->version($id);
        $pdf->generate($version);

        return response()->json(['status' => true, 'message' => 'PDF generated', 'share' => $share->buildDraftForVersion($version->fresh())]);
    }

    public function createOptions(string $id)
    {
        $inquiry = Inquiry::where('uuid', $id)->firstOrFail();
        abort_if($inquiry->serviceDetails, 422, 'Use the service request quotation form.');
        $data = app()->call([app(QuotationController::class), 'createFromInquiry'], ['id' => $id])->getData();

        return response()->json($data);
    }

    public function create(Request $request, string $id, QuoteBuilderService $builder)
    {
        $inquiry = Inquiry::where('uuid', $id)->firstOrFail();
        abort_if($inquiry->serviceDetails, 422, 'Use the service request quotation form.');
        abort_unless($inquiry->is_approved, 422, 'Approve the inquiry first.');
        $data = $request->validate(['title' => 'required|string|max:255', 'trip_id' => 'nullable|uuid|exists:trips,uuid', 'subtitle' => 'nullable|string|max:255', 'introduction' => 'nullable|string', 'internal_notes' => 'nullable|string', 'currency_id' => 'required|integer|exists:currencies,id', 'service_class_id' => 'nullable|integer|exists:service_classes,id']);
        $version = $builder->createFromInquiry($inquiry, $data, $request->user()->id);

        return response()->json(['status' => true, 'version_uuid' => $version->uuid, 'quotation_uuid' => $version->quotation->uuid]);
    }
}
