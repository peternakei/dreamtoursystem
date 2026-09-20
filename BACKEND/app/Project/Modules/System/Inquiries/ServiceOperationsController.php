<?php

namespace App\Project\Modules\System\Inquiries;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Bookings\BookingStatus;
use App\Project\Modules\System\Inquiries\Services\ServiceInquiryService;
use App\Project\Modules\System\Pages\ContentSupport;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use App\Project\Modules\System\Quotations\Services\QuotePricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceOperationsController extends Controller
{
    private function inquiry(string $uuid)
    {
        return Inquiry::with('serviceDetails')->whereHas('serviceDetails')->where('uuid', $uuid)->firstOrFail();
    }

    public function index(Request $request)
    {
        $request->validate(['service_type' => 'nullable|in:rental,business,international,local,group,safari,flight', 'limit' => 'nullable|integer|min:1|max:100', 'offset' => 'nullable|integer|min:0']);
        $query = Inquiry::with('serviceDetails')->whereHas('serviceDetails', fn ($q) => $request->filled('service_type') ? $q->where('service_type', $request->service_type) : $q)->latest('id');

        return ContentSupport::listing($query, $request, 'inquiries', fn ($i) => ServiceInquiryService::resource($i, true));
    }

    public function show(string $uuid)
    {
        $inquiry = $this->inquiry($uuid);

        return ContentSupport::success(['inquiry' => ServiceInquiryService::resource($inquiry, true), 'quotes' => self::quotes($inquiry, true), 'currencies' => Currency::all(['id', 'uuid', 'name', 'short_name', 'symbol']), 'staff' => User::where('profile', 'SystemUser')->where('is_active', true)->get(['id', 'username'])]);
    }

    public static function quotes(Inquiry $inquiry, bool $staff = false): array
    {
        return QuotationVersion::with('currency')->whereHas('quotation', fn ($q) => $q->where('inquiry_id', $inquiry->id))
            ->when(! $staff, fn ($q) => $q->whereIn('status', ['sent', 'accepted']))->orderByDesc('id')->get()->map(fn ($v) => [
                'uuid' => $v->uuid, 'reference_number' => $v->reference_number, 'title' => $v->title, 'status' => $v->status,
                'amount' => number_format((float) $v->amount, 2, '.', ''), 'vat_amount' => number_format((float) $v->vat_amount, 2, '.', ''), 'total_amount' => number_format((float) $v->total_amount, 2, '.', ''),
                'currency' => $v->currency?->only(['id', 'short_name', 'symbol']), 'service_context' => $v->service_context,
            ])->all();
    }

    public function update(Request $request, string $uuid)
    {
        $data = $request->validate([
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where('profile', 'SystemUser')->where('is_active', true)],
            'comments' => 'nullable|string', 'is_approved' => 'required|boolean',
            'operations' => 'required|array:internal_notes,customer_notes,supply_verified,issuance_state,booking_reference,issued_at',
            'operations.internal_notes' => 'nullable|string', 'operations.customer_notes' => 'nullable|string',
            'operations.supply_verified' => 'nullable|boolean', 'operations.issuance_state' => 'nullable|in:not_issued,confirmed,issued,cancelled',
            'operations.booking_reference' => 'nullable|string|max:255', 'operations.issued_at' => 'nullable|date|before_or_equal:now',
        ]);
        $inquiry = $this->inquiry($uuid);
        $detail = $inquiry->serviceDetails;
        $ops = $data['operations'];
        if (in_array($ops['issuance_state'] ?? null, ['confirmed', 'issued'], true)) {
            if ($detail->service_type !== 'flight' || ! $detail->booking_id || empty($ops['booking_reference'])) {
                ServiceInquiryService::invalid('operations.issuance_state', 'A recorded flight booking and actual airline reference are required.');
            }
            if ($ops['issuance_state'] === 'issued' && empty($ops['issued_at'])) {
                ServiceInquiryService::invalid('operations.issued_at', 'Record the actual ticket issuance time.');
            }
        } elseif (($ops['issuance_state'] ?? null) === 'cancelled') {
            $previous = $detail->operations ?? [];
            if ($detail->service_type !== 'flight' || ! $detail->booking_id || empty($previous['booking_reference']) || ! in_array($previous['issuance_state'] ?? null, ['confirmed', 'issued', 'cancelled'], true)) {
                ServiceInquiryService::invalid('operations.issuance_state', 'Only a previously confirmed or issued flight can be marked cancelled.');
            }
            if (! empty($ops['booking_reference']) && $ops['booking_reference'] !== $previous['booking_reference']) {
                ServiceInquiryService::invalid('operations.booking_reference', 'Cancellation must retain the recorded airline reference.');
            }
            $ops['booking_reference'] = $previous['booking_reference'];
            $ops['issued_at'] = $previous['issued_at'] ?? null;
        } elseif (! empty($ops['booking_reference']) || ! empty($ops['issued_at'])) {
            ServiceInquiryService::invalid('operations.booking_reference', 'A booking reference or issuance date is only recorded for confirmed or issued flights.');
        }
        if (! empty($ops['supply_verified'])) {
            $ops['supply_verified_by'] = $request->user()->id;
            $ops['supply_verified_at'] = now()->toIso8601String();
        }
        DB::transaction(function () use ($inquiry, $detail, $ops, $data, $request) {
            $inquiry->update(['assigned_to' => $data['assigned_to'] ?? null, 'comments' => $data['comments'] ?? null, 'is_approved' => $data['is_approved'], 'updated_by' => $request->user()->id]);
            $detail->update(['operations' => array_merge($detail->operations ?? [], $ops)]);
        });

        return $this->show($uuid);
    }

    public function quote(Request $request, string $uuid, QuoteBuilderService $builder, QuotePricingService $pricing)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255', 'currency_id' => 'required|integer|exists:currencies,id', 'valid_until' => 'required|date|after:now',
            'status' => 'required|in:draft,sent', 'vat_enabled' => 'required|boolean', 'terms' => 'required|string',
            'price_lines' => 'required|array|min:1', 'price_lines.*' => 'array:description,quantity,unit_price',
            'price_lines.*.description' => 'required|string|max:255', 'price_lines.*.quantity' => 'required|integer|min:1|max:4294967295',
            'price_lines.*.unit_price' => 'required|numeric|gt:0|max:9999999999999.99|decimal:0,2',
            'flight_offer' => 'nullable|array:itinerary,airline,cabin,baggage,fare_conditions',
            'flight_offer.itinerary' => 'required_with:flight_offer|string', 'flight_offer.airline' => 'required_with:flight_offer|string',
            'flight_offer.cabin' => 'required_with:flight_offer|string', 'flight_offer.baggage' => 'required_with:flight_offer|string', 'flight_offer.fare_conditions' => 'required_with:flight_offer|string',
        ]);
        if (collect($data['price_lines'])->sum(fn ($line) => (float) $line['unit_price'] * (int) $line['quantity']) * ($data['vat_enabled'] ? 1.18 : 1) > 9999999999999.99) {
            ServiceInquiryService::invalid('price_lines', 'Quotation exceeds the existing decimal amount capacity.');
        }
        $inquiry = $this->inquiry($uuid);
        if ($inquiry->serviceDetails->service_type === 'flight' && empty($data['flight_offer'])) {
            ServiceInquiryService::invalid('flight_offer', 'A flight quote needs itinerary, airline, cabin, baggage and fare conditions.');
        }
        $version = DB::transaction(function () use ($data, $inquiry, $request, $builder, $pricing) {
            $version = $builder->createFromInquiry($inquiry, ['title' => $data['title'], 'currency_id' => $data['currency_id'], 'introduction' => 'Proposal for your requested '.str_replace('_', ' ', $inquiry->serviceDetails->service_type).' service.'], $request->user()->id);
            $version->days()->get()->each(fn ($day) => $day->delete());
            $builder->syncPaymentTerms($version, [], $request->user()->id);
            $version->update(['status' => $data['status'], 'sent_at' => $data['status'] === 'sent' ? now() : null, 'vat_enabled' => $data['vat_enabled'],
                'service_context' => ['valid_until' => $data['valid_until'], 'terms' => $data['terms'], 'flight_offer' => $data['flight_offer'] ?? null,
                    'inquiry_uuid' => $inquiry->uuid, 'service_type' => $inquiry->serviceDetails->service_type, 'request_details' => $inquiry->serviceDetails->request_details],
            ]);
            $rows = array_map(fn ($row) => $row + ['currency_id' => $data['currency_id'], 'source_type' => 'manual', 'source_id' => null, 'traveler_type' => null, 'is_optional' => false, 'is_visible' => true], $data['price_lines']);
            $pricing->syncPriceLines($version, $rows, $request->user()->id);
            $builder->syncTerms($version, ['included' => [['description' => $data['terms']]], 'excluded' => []], $request->user()->id);

            return $version;
        });

        return ContentSupport::success(['quotation_uuid' => $version->uuid, 'quotes' => self::quotes($inquiry, true)], 'Quotation saved using the existing quotation engine');
    }

    public function book(Request $request, string $uuid)
    {
        $data = $request->validate(['quotation_uuid' => 'required|uuid', 'customer_agreed' => 'required|accepted']);
        $result = DB::transaction(function () use ($uuid, $data, $request) {
            $inquiry = $this->inquiry($uuid);
            $detail = InquiryServiceDetail::where('inquiry_id', $inquiry->id)->lockForUpdate()->firstOrFail();
            if ($detail->booking_id) {
                return Booking::findOrFail($detail->booking_id);
            }
            $version = QuotationVersion::where('uuid', $data['quotation_uuid'])->whereHas('quotation', fn ($q) => $q->where('inquiry_id', $inquiry->id))->lockForUpdate()->first();
            if (! $version || ! $version->service_context || $version->status !== 'sent' || $version->total_amount <= 0) {
                ServiceInquiryService::invalid('quotation_uuid', 'Select a sent service quotation with a positive agreed amount.');
            }
            if (Carbon::parse($version->service_context['valid_until'])->isPast()) {
                ServiceInquiryService::invalid('quotation_uuid', 'The quotation has expired.');
            }
            if ($detail->service_type === 'rental' && empty($detail->operations['supply_verified'])) {
                ServiceInquiryService::invalid('supply_verified', 'Staff must verify vehicle supply before recording a rental booking.');
            }
            $status = BookingStatus::where('name', 'Reserved')->value('id');
            if (! $status) {
                ServiceInquiryService::invalid('booking_status', 'Configure the existing Reserved booking status first.');
            }
            $booking = Booking::create(['tourist_id' => $inquiry->tourist_id, 'trip_id' => null, 'trip_group_id' => null, 'booking_type_id' => null,
                'booking_date' => $inquiry->from_date->format('Y-m-d'), 'guest_count' => $inquiry->guests, 'amount' => $version->amount, 'vat_amount' => $version->vat_amount,
                'total_amount' => $version->total_amount, 'currency_id' => $version->currency_id, 'remarks' => 'Service inquiry '.$inquiry->inquiry_code,
                'comments' => 'Booked from quotation '.$version->reference_number, 'booking_status_id' => $status, 'created_by' => $request->user()->id]);
            $detail->update(['booking_id' => $booking->id, 'quotation_version_id' => $version->id, 'agreed_price' => [
                'amount' => $version->amount, 'vat_amount' => $version->vat_amount, 'total_amount' => $version->total_amount, 'currency_id' => $version->currency_id,
                'quotation_reference' => $version->reference_number, 'terms' => $version->service_context['terms'], 'agreed_at' => now()->toIso8601String(),
            ]]);
            $version->update(['booking_id' => $booking->id, 'status' => 'accepted', 'accepted_at' => now()]);

            return $booking;
        });

        return ContentSupport::success(['booking' => ['uuid' => $result->uuid, 'booking_number' => $result->booking_number, 'booking_status_id' => $result->booking_status_id]], 'Agreed booking recorded; payment and confirmation remain separate');
    }
}
