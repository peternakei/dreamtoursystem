<?php

namespace App\Project\Workspace\Services;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Genders\Gender;
use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Invoices\Invoice;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Quotations\QuotationVersion;
use App\Project\Modules\System\Receipts\Receipt;
use App\Project\Modules\System\Tourists\Tourist;

/** Structured workspace data; no legacy layout is rendered for these screens. */
class OperationsPageService
{
    private const MODELS = ['bookings' => Booking::class, 'tourists' => Tourist::class, 'invoices' => Invoice::class, 'receipts' => Receipt::class];

    private const RELATIONS = [
        'bookings' => ['tourist.country', 'trip.tripType', 'currency', 'status', 'bookingType'],
        'tourists' => ['country', 'gender'],
        'invoices' => ['tourist', 'booking', 'currency', 'status'],
        'receipts' => ['invoice.tourist', 'currency', 'paymentMode'],
    ];

    public static function supports(string $slug): bool
    {
        return isset(self::MODELS[$slug]);
    }

    public static function page(string $slug, ?string $id): array
    {
        $query = self::MODELS[$slug]::with(self::RELATIONS[$slug]);
        $page = ['module' => PageService::definition($slug), 'records' => [], 'details' => [], 'forms' => [], 'sections' => [], 'links' => [], 'notices' => []];
        if ($id === null) {
            $page['records'] = $query->latest()->get();
            if ($slug === 'tourists') {
                $page['forms'] = self::touristForms();
            }

            return $page;
        }
        $record = $query->where('uuid', $id)->firstOrFail();
        $page['details'] = ['record' => $record];
        if ($slug === 'bookings') {
            $record->load(['invoices.currency', 'invoices.status', 'invoices.tourist', 'invoices.booking', 'payments.currency', 'payments.paymentMode', 'payments.invoice']);
            $page['sections'] = [self::section('Invoices', 'invoices', $record->invoices), self::section('Payments', 'receipts', $record->payments)];
            self::link($page, 'Customer profile', 'tourists', $record->tourist);
            self::link($page, 'Trip details', 'trips', $record->trip);
            $version = QuotationVersion::with('quotation.inquiry.serviceDetails')->where('booking_id', $record->id)->first();
            if ($version) {
                self::link($page, 'Source quotation', 'quotations', $version->quotation);
                $inquiry = $version->quotation?->inquiry;
                if ($inquiry?->serviceDetails) {
                    $page['links'][] = ['label' => 'Manage service booking', 'to' => '/inquiries/'.$inquiry->uuid.'/services'];
                }
            }
            $page['notices'][] = 'For changes or cancellation, use the originating service request when available. This page shows the recorded booking and its financial documents.';
        } elseif ($slug === 'tourists') {
            $page['forms'] = self::touristForms($record);
            $page['sections'] = [
                self::section('Bookings', 'bookings', Booking::with(self::RELATIONS['bookings'])->where('tourist_id', $record->id)->latest()->get()),
                self::section('Inquiries', 'inquiries', Inquiry::with(['tourist', 'serviceDetails'])->withExists('serviceDetails')->withCount('quotations')->where('tourist_id', $record->id)->latest()->get()),
                self::section('Quotations', 'quotations', Quotation::with(['tourist', 'currency', 'status', 'currentVersion.currency'])->where('tourist_id', $record->id)->latest()->get()),
            ];
        } elseif ($slug === 'invoices') {
            $record->load(['payments.currency', 'payments.paymentMode', 'payments.invoice']);
            $page['sections'] = [self::section('Payments', 'receipts', $record->payments)];
            self::link($page, 'Booking details', 'bookings', $record->booking);
            self::link($page, 'Customer profile', 'tourists', $record->tourist);
        } elseif ($slug === 'receipts') {
            self::link($page, 'Invoice details', 'invoices', $record->invoice);
            self::link($page, 'Customer profile', 'tourists', $record->invoice?->tourist);
        }

        return $page;
    }

    private static function section(string $title, string $module, $rows): array
    {
        return compact('title', 'module', 'rows');
    }

    private static function link(array &$page, string $label, string $module, $record): void
    {
        if ($record) {
            $page['links'][] = ['label' => $label, 'to' => '/'.$module.'/'.$record->uuid.'/details'];
        }
    }

    private static function field(string $name, string $label, string $type = 'text', $value = '', bool $required = false, array $options = []): array
    {
        return compact('name', 'label', 'type', 'value', 'required', 'options') + ['multiple' => false, 'disabled' => false, 'min' => '', 'max' => '', 'maxlength' => '', 'placeholder' => ''];
    }

    private static function touristForms(?Tourist $tourist = null): array
    {
        $forms = [];
        $user = auth()->user();
        if ($user?->can($tourist ? 'edit-tourists' : 'create-tourists')) {
            $options = fn ($records) => array_merge([['value' => '', 'label' => 'Select…']], $records->map(fn ($r) => ['value' => (string) $r->id, 'label' => $r->name])->all());
            $forms[] = ['recordKey' => $tourist?->uuid, 'title' => $tourist ? 'Edit tourist' : 'Create tourist', 'action' => '/tourists'.($tourist ? '/'.$tourist->uuid : ''), 'method' => $tourist ? 'PUT' : 'POST', 'fields' => [
                self::field('name', 'Name', 'text', $tourist ? html_entity_decode($tourist->name, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '', true),
                self::field('email', 'Email', 'email', $tourist?->email ?? '', true),
                self::field('phone', 'Phone', 'tel', $tourist?->phone ?? '', true),
                self::field('gender', 'Gender', 'select', $tourist?->gender_id ?? '', true, $options(Gender::orderBy('name')->get())),
                self::field('country', 'Country', 'select', $tourist?->country_id ?? '', true, $options(Country::orderBy('name')->get())),
                self::field('address', 'Address', 'textarea', $tourist?->address ?? ''),
            ]];
        }
        if ($tourist) {
            $forms[] = ['recordKey' => $tourist->uuid, 'title' => 'Change tourist status', 'action' => '/tourists/change_status/'.$tourist->uuid, 'method' => 'POST', 'fields' => [
                self::field('new_status', 'New status', 'select', $tourist->is_active ? '2' : '1', true, [['value' => '1', 'label' => 'Active'], ['value' => '2', 'label' => 'Inactive']]),
            ]];
        }
        if ($tourist && $user?->can('delete-tourists')) {
            $forms[] = ['recordKey' => $tourist->uuid, 'title' => 'Delete tourist', 'description' => 'Delete this tourist profile and its login account? Review any linked bookings before confirming.', 'action' => '/tourists/'.$tourist->uuid, 'method' => 'DELETE', 'fields' => []];
        }

        return $forms;
    }
}
