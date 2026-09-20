<?php

namespace App\Project\Workspace\Services;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripPlannerController;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WorkspaceToolsService
{
    // These resource editors already exist in the source application.
    private const EDITABLE = ['activities', 'addons', 'bank_details', 'banks', 'budgets', 'countries', 'destinations', 'exchange_rates', 'faqs', 'locations', 'menus', 'permissions', 'roles', 'trips', 'users'];

    private const INLINE = ['accommodations', 'categories', 'vehicles'];

    public static function enhance(string $slug, ?string $id, array $page): array
    {
        if (OperationsPageService::supports($slug) || in_array($slug, ['inquiries', 'quotations', 'pages'])) {
            return $page;
        }
        if (! $id) {
            return $page;
        }
        $record = collect($page['details'])->first();
        if (! $record instanceof Model) {
            return $page;
        }
        $definition = PageService::definition($slug);
        $controller = app($definition['controller']);
        $path = $definition['path'].'/'.$id;
        if (in_array($slug, array_merge(self::EDITABLE, self::INLINE), true)) {
            $index = app()->call([$controller, 'index']);
            if ($index instanceof View) {
                $templates = PageService::forms($index->render(), $definition['path'], $id);
                foreach ($templates as $template) {
                    if ($template['action'] !== $path || ! in_array($template['method'], ['PUT', 'DELETE'])) {
                        continue;
                    }
                    if (collect($page['forms'])->contains(fn ($f) => $f['action'] === $path && $f['method'] === $template['method'])) {
                        continue;
                    }
                    $ability = ($template['method'] === 'DELETE' ? 'delete-' : 'edit-').str_replace('_', '-', $slug);
                    if (! auth()->user()?->can($ability) && ! in_array($slug, self::INLINE)) {
                        continue;
                    }
                    if ($template['method'] === 'PUT') {
                        $response = app()->call([$controller, 'edit'], ['id' => $id]);
                        if (! $response instanceof JsonResponse) {
                            continue;
                        }
                        $data = $response->getData(true);
                        $template = self::fill($template, $data, $record, $slug);
                        $template['title'] = 'Edit details';
                    } else {
                        $template['title'] = 'Delete record';
                        $template['description'] = 'Delete this record? Review any linked records before confirming.';
                    }
                    $page['forms'][] = $template;
                }
            }
        }
        if ($slug === 'trips') {
            $record->loadMissing(['groups', 'groupCamps']);
        }
        // Sections expose existing relationships rather than printing raw IDs/JSON.
        $page['sections'] = $page['sections'] ?? [];
        foreach ($record->getRelations() as $name => $items) {
            if (! $items instanceof Collection || in_array($name, ['gallery', 'covers', 'videos', 'images', 'banners', 'media'])) {
                continue;
            }
            $rows = [];
            foreach ($items as $item) {
                if (! $item instanceof Model) {
                    continue;
                }
                $row = ['uuid' => $item->uuid ?? (string) $item->getKey()];
                foreach ($item->toArray() as $key => $value) {
                    if (preg_match('/(^id$|_id$|^uuid$|password|token|secret|created_by|updated_by|deleted_at)/i', $key)) {
                        continue;
                    }
                    if (is_scalar($value) || $value === null) {
                        $row[$key] = $value;
                    } elseif (is_array($value) && ! array_is_list($value)) {
                        $row[$key] = $value['name'] ?? $value['title'] ?? $value['short_name'] ?? null;
                    }
                }
                $row['_forms'] = self::rowForms($slug, $record, $name, $item);
                $rows[] = $row;
            }
            $keys = array_values(array_unique(array_merge(...array_map(fn ($r) => array_keys($r), $rows ?: [[]]))));
            $columns = array_map(fn ($key) => ['key' => $key, 'label' => Str::headline($key), 'sortable' => true], array_values(array_filter($keys, fn ($key) => ! in_array($key, ['uuid', '_forms']) && ! str_ends_with($key, '_at'))));
            if ($columns) {
                $page['sections'][] = ['title' => Str::headline($name), 'module' => '', 'rows' => $rows, 'columns' => $columns];
            }
        }
        if (array_key_exists($slug, config('library.entities', [])) && method_exists($record, 'media')) {
            // Include legacy destination attachments whose role predates library media.
            $images = $slug === 'destinations' ? $record->images()->orderBy('sort_order')->get() : $record->media()->orderBy('sort_order')->get();
            $page['media'] = ['type' => $slug, 'uuid' => $id, 'images' => $images->map(fn ($m) => array_merge($m->toArray(), ['url' => $m->url, 'role' => $m->role ?: 'gallery']))->all()];
            $page['forms'][] = self::form('Edit formatted description', '/library/'.$slug.'/'.$id.'/description', 'PATCH', [self::field('description', 'Description', 'richtext', html_entity_decode($record->description ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'))], $id);
        }
        if ($slug === 'trips') {
            $page['documents'] = $record->banners->map(fn ($m) => ['url' => $m->url, 'title' => $m->title ?: 'Trip banner'])->all();
            $page['planner'] = app()->call([app(TripPlannerController::class), 'edit'], ['id' => $id])->getData();
            $selectedIds = collect($page['planner']['seedRows'])->pluck('destination_id')->filter();
            $page['planner']['destinations'] = $page['planner']['destinations']->merge(Destination::whereIn('id', $selectedIds)->get())->unique('id')->values();
            // Query group choices for this trip, not every trip in the database.
            foreach ($page['forms'] as &$form) {
                foreach ($form['fields'] as &$field) {
                    if ($field['name'] === 'group' && $field['type'] === 'select') {
                        $field['options'] = $record->groups()->get()->map(fn ($g) => ['value' => (string) $g->id, 'label' => $g->group])->all();
                    }
                }
            }
            unset($form,$field);
        }

        return $page;
    }

    private static function fill(array $form, array $data, Model $record, string $slug): array
    {
        $values = $data['data'] ?? $record->toArray();
        $catalogNames = ['country' => 'countries', 'gender' => 'genders', 'bank' => 'banks', 'currency' => 'currencies', 'location' => 'locations', 'region' => 'regions', 'category' => 'categories', 'season' => 'seasons', 'class' => 'classes', 'trip' => 'trips', 'type' => 'tripTypes', 'source' => 'tripSources', 'parent' => 'menus', 'is_include' => 'isIncludes'];
        $attributes = ['type' => 'trip_type_id', 'source' => 'trip_source_id', 'class' => 'service_class_id', 'parent' => 'menu_id', 'category' => $slug === 'faqs' ? 'faq_category_id' : 'category_id'];
        foreach ($form['fields'] as &$field) {
            $name = $field['name'];
            $key = $attributes[$name] ?? (array_key_exists($name.'_id', $values) ? $name.'_id' : $name);
            $value = $values[$key] ?? '';
            if ($name === 'is_include') {
                $value = ! empty($values['is_include']) ? '1' : '2';
            }
            if ($field['type'] === 'date' && $value) {
                $value = substr((string) $value, 0, 10);
            }
            $field['value'] = is_string($value) ? html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $value;
            if ($field['type'] === 'select') {
                $catalog = $data[$catalogNames[$name] ?? ''] ?? null;
                if (is_array($catalog)) {
                    $field['options'] = array_merge([['value' => '', 'label' => 'Select…']], array_map(fn ($r) => ['value' => (string) $r['id'], 'label' => $r['name'] ?? $r['short_name'] ?? $r['title'] ?? ''], $catalog));
                }
            }
        }
        unset($field);
        if (in_array($slug, ['roles', 'permissions'])) {
            $name = $slug === 'roles' ? 'permission' : 'role';
            $catalog = $data[$slug === 'roles' ? 'permissions' : 'roles'] ?? [];
            $field = self::field($name.'[]', ucfirst($name).'s to add (existing assignments are retained)', 'select', $data[$slug === 'roles' ? 'selectedPermissions' : 'selectedRoles'] ?? [], true);
            $field['multiple'] = true;
            $field['options'] = array_map(fn ($r) => ['value' => (string) $r['id'], 'label' => $r['name']], $catalog);
            $form['fields'][] = $field;
        }

        return $form;
    }

    private static function rowForms(string $slug, Model $record, string $relation, Model $item): array
    {
        if ($slug === 'destinations' && $relation === 'facts') {
            return [self::form('Edit fact', '/destinations/update_destination_fact/'.$record->uuid.'/'.$item->uuid, 'PUT', [
                self::field('fact', 'Fact', 'text', html_entity_decode($item->fact), true), self::field('sub_fact', 'Sub fact', 'text', html_entity_decode($item->sub_fact ?? '')), self::field('description', 'Description', 'textarea', html_entity_decode($item->description ?? ''), true),
            ], $item->uuid)];
        }
        if ($slug === 'trips' && $relation === 'points') {
            return [self::form('Edit point', '/trips/update_point/'.$item->uuid.'/'.$record->uuid, 'PUT', [self::field('title', 'Title', 'text', html_entity_decode($item->title), true), self::field('description', 'Description', 'textarea', html_entity_decode($item->description ?? ''), true)], $item->uuid), self::form('Delete point', '/trips/delete_point/'.$item->uuid, 'DELETE', [], $item->uuid)];
        }
        if ($slug === 'trips' && $relation === 'addons') {
            return [self::form('Remove addon', '/trips/delete_addon/'.$item->uuid, 'DELETE', [], $item->uuid)];
        }

        return [];
    }

    public static function field(string $name, string $label, string $type = 'text', $value = '', bool $required = false): array
    {
        return compact('name', 'label', 'type', 'value', 'required') + ['options' => [], 'multiple' => false, 'disabled' => false, 'min' => '', 'max' => '', 'maxlength' => '', 'placeholder' => '', 'step' => 'any'];
    }

    private static function form(string $title, string $action, string $method, array $fields, ?string $recordKey): array
    {
        return compact('title', 'action', 'method', 'fields', 'recordKey');
    }
}
