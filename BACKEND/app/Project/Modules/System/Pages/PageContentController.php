<?php

namespace App\Project\Modules\System\Pages;

use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Quotations\Services\QuotePricingService;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Vehicles\RentalOffer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PageContentController extends Controller
{
    public function index()
    {
        return ContentSupport::success(['pages' => Page::with('media')->orderBy('sort_order')->get()->map(fn ($p) => array_merge($p->toArray(), ['images' => ContentSupport::media($p)]))]);
    }

    public function show(Request $request, string $name)
    {
        $request->validate(['locale' => 'nullable|in:en,fr,sw']);
        $locale = $request->input('locale', 'en');
        $page = Page::where('name', $name)->where('is_published', true)->firstOrFail();
        $sections = collect($page->sections ?? [])->map(function ($section) use ($locale) {
            if (! empty($section['reference_uuid'])) {
                $model = match ($section['reference_type'] ?? '') {
                    'trip' => Trip::class,'destination' => Destination::class,'rental_offer' => RentalOffer::class,default => null
                };
                $query = $model ? $model::where('uuid', $section['reference_uuid']) : null;
                if ($model === Trip::class) {
                    $query->where('is_published', true)->whereHas('tripStatus', fn ($q) => $q->where('name', 'Approved'));
                }
                if ($model === RentalOffer::class) {
                    $query->where('is_active', true)->where('is_published', true);
                }
                if ($model === Destination::class) {
                    $query->where('is_active', true);
                }
                if (! $query?->exists()) {
                    return null;
                }
            }
            $translations = $section['translations'] ?? [];
            unset($section['translations']);

            return ContentSupport::localized($section, $translations, $locale);
        })->filter()->values()->all();
        $data = ContentSupport::localized(['uuid' => $page->uuid, 'name' => $page->name, 'title' => $page->title, 'sub_title' => $page->sub_title, 'description' => $page->description,
            'seo_title' => $page->seo['title'] ?? $page->title, 'seo_description' => $page->seo['description'] ?? '',
            'sections' => $sections, 'images' => ContentSupport::media($page), 'updated_at' => $page->updated_at], $page->translations, $locale);

        return ContentSupport::success(['page' => $data]);
    }

    public function save(Request $request, ?string $uuid = null)
    {
        $page = $uuid ? Page::where('uuid', $uuid)->firstOrFail() : new Page;
        $urlRule = function ($attribute, $value, $fail) {
            if ($value && ! preg_match('~^(https?://|/(?!/))~i', $value)) {
                $fail('Use an http(s) URL or a path starting with /.');
            }
        };
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('pages', 'name')->ignore($page->id)],
            'title' => 'required|string|max:255', 'sub_title' => 'nullable|string|max:255', 'description' => 'required|string',
            'is_published' => 'required|boolean', 'sort_order' => 'required|integer|min:0', 'seo' => 'nullable|array:title,description',
            'seo.title' => 'nullable|string|max:255', 'seo.description' => 'nullable|string',
            'sections' => 'present|array', 'sections.*' => 'array:key,type,title,text,value,cta_label,cta_url,media_uuid,reference_type,reference_uuid,translations',
            'sections.*.key' => 'required|string|alpha_dash|distinct|max:80',
            'sections.*.type' => 'required|in:hero,service,gallery,featured,process,values,statistics,consultation,contact',
            'sections.*.title' => 'nullable|string|max:255', 'sections.*.text' => 'nullable|string', 'sections.*.value' => 'nullable|string|max:255',
            'sections.*.cta_label' => 'nullable|string|max:255', 'sections.*.cta_url' => ['nullable', 'string', 'max:2048', $urlRule],
            'sections.*.media_uuid' => 'nullable|uuid', 'sections.*.reference_type' => 'nullable|in:trip,destination,rental_offer',
            'sections.*.reference_uuid' => 'nullable|uuid',
            ...ContentSupport::translationRules(['title', 'sub_title', 'description', 'seo_title', 'seo_description']),
        ] + self::sectionTranslationRules());
        foreach ($data['sections'] as $i => $section) {
            if (! empty($section['media_uuid']) && (! $page->exists || ! $page->media()->where('uuid', $section['media_uuid'])->exists())) {
                throw ValidationException::withMessages(['sections.'.$i.'.media_uuid' => 'Choose media uploaded to this page.']);
            }
            if (! empty($section['reference_uuid']) || ! empty($section['reference_type'])) {
                $model = match ($section['reference_type'] ?? '') {
                    'trip' => Trip::class,'destination' => Destination::class,'rental_offer' => RentalOffer::class,default => null
                };
                if (! $model || ! $model::where('uuid', $section['reference_uuid'] ?? '')->exists()) {
                    throw ValidationException::withMessages(['sections.'.$i.'.reference_uuid' => 'Choose an existing content record.']);
                }
            }
        }
        $page->fill($data);
        $page->{$page->exists ? 'updated_by' : 'created_by'} = $request->user()->id;
        $page->save();

        return ContentSupport::success(['page' => $page], 'Page content saved');
    }

    private static function sectionTranslationRules(): array
    {
        $rules = ['sections.*.translations' => 'nullable|array:en,fr,sw'];
        foreach (['en', 'fr', 'sw'] as $locale) {
            $rules['sections.*.translations.'.$locale] = 'nullable|array:title,text,cta_label';
            foreach (['title', 'text', 'cta_label'] as $field) {
                $rules['sections.*.translations.'.$locale.'.'.$field] = 'nullable|string';
            }
        }

return $rules;
    }

    public function preferences(QuotePricingService $pricing)
    {
        return ContentSupport::success(['locales' => ['en', 'fr', 'sw'], 'default_locale' => 'en', 'currencies' => Currency::all(['id', 'uuid', 'name', 'short_name', 'symbol']),
            'default_currency_id' => $pricing->defaultCurrencyId(), 'rental_timezone' => config('app.timezone'), 'rental_availability' => 'manual_verification_required', 'rental_pricing' => 'quotation_only']);
    }

    public function travelOptions()
    {
        return ContentSupport::success(['trips' => Trip::orderBy('name')->get(['uuid', 'name', 'description', 'service_details', 'translations']), 'destinations' => Destination::orderBy('name')->get(['uuid', 'name', 'description', 'translations'])]);
    }

    public function saveTravel(Request $request, string $type, string $uuid)
    {
        abort_unless(in_array($type, ['trips', 'destinations'], true), 404);
        $model = $type === 'trips' ? Trip::class : Destination::class;
        $entity = $model::where('uuid', $uuid)->firstOrFail();
        $rules = ContentSupport::translationRules(['name', 'description']);
        if ($type === 'trips') {
            $rules += [
                'service_details' => 'nullable|array:purpose,geography,company_services,industry,route,duration,travellers,transport,accommodation,inclusions,terms,price_basis',
                'service_details.purpose' => 'nullable|string|max:255', 'service_details.geography' => 'nullable|in:local,international',
                'service_details.company_services' => 'nullable|string', 'service_details.industry' => 'nullable|string|max:255', 'service_details.route' => 'nullable|string',
                'service_details.duration' => 'nullable|string|max:255', 'service_details.travellers' => 'nullable|integer|min:1', 'service_details.transport' => 'nullable|string',
                'service_details.accommodation' => 'nullable|string', 'service_details.inclusions' => 'nullable|string', 'service_details.terms' => 'nullable|string', 'service_details.price_basis' => 'nullable|string|max:255',
            ];
        }
        $entity->update($request->validate($rules) + ['updated_by' => $request->user()->id]);

        return ContentSupport::success(['record' => $entity], 'Travel content saved');
    }

    public function travel(Request $request, string $type, string $uuid)
    {
        $request->validate(['locale' => 'nullable|in:en,fr,sw']);
        abort_unless(in_array($type, ['trips', 'destinations'], true), 404);
        $query = $type === 'trips' ? Trip::where('is_published', true)->whereHas('tripStatus', fn ($q) => $q->where('name', 'Approved')) : Destination::where('is_active', true);
        $entity = $query->where('uuid', $uuid)->firstOrFail();

        return ContentSupport::success(['record' => ContentSupport::localized(['uuid' => $entity->uuid, 'name' => $entity->name, 'description' => $entity->description, 'service_details' => $entity->service_details], $entity->translations, $request->input('locale','en'))]);
    }
}
