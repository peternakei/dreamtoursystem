<?php

namespace App\Project\Workspace\Services;

use App\Project\Modules\System\Blogs\Blog;
use App\Project\Modules\System\Pages\Page;
use App\Project\Modules\System\Refunds\Refund;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use DOMDocument;
use DOMXPath;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PageService
{
    public static function modules(): array
    {
        return json_decode(file_get_contents(app_path('Project/_Src/workspace.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function definition(string $slug): array
    {
        return self::modules()[$slug] ?? abort(404);
    }

    public static function page(string $slug, ?string $id = null): array
    {
        return WorkspaceToolsService::enhance($slug, $id, self::basePage($slug, $id));
    }

    private static function basePage(string $slug, ?string $id = null): array
    {
        $definition = self::definition($slug);
        if (OperationsPageService::supports($slug)) {
            return OperationsPageService::page($slug, $id);
        }
        $controller = app($definition['controller']);
        $view = $id === null ? app()->call([$controller, 'index']) : app()->call([$controller, 'show'], ['id' => $id]);
        if (! $view instanceof View && $id !== null) {
            $listView = app()->call([$controller, 'index']);
            if ($listView instanceof View) {
                foreach ($listView->getData() as $key => $items) {
                    if (str_replace('_', '', strtolower($key)) !== str_replace('_', '', strtolower($slug))) {
                        continue;
                    }
                    if ($items instanceof Paginator) {
                        $items = collect($items->items());
                    }
                    if (! $items instanceof Collection) {
                        continue;
                    }
                    $record = $items->first(fn ($item) => (string) ($item->uuid ?? $item->id) === $id);
                    abort_unless($record, 404);

                    return ['module' => $definition, 'records' => [], 'details' => ['record' => $record], 'forms' => []];
                }
            }
        }
        if (! $view instanceof View) {
            $models = ['blogs' => Blog::class, 'pages' => Page::class, 'refunds' => Refund::class];
            abort_unless(isset($models[$slug]), 404, 'This workflow is not implemented in the source application.');
            $model = $models[$slug];

            return ['module' => $definition, 'records' => $id === null ? $model::query()->latest()->get() : [], 'details' => $id !== null ? ['record' => $model::where('uuid', $id)->firstOrFail()] : [], 'forms' => []];
        }
        $data = $view->getData();
        $records = [];
        $details = [];
        foreach ($data as $key => $value) {
            if ($value instanceof Model) {
                $details[$key] = $value;
            }
            if (str_replace('_', '', strtolower($key)) === str_replace('_', '', strtolower($slug)) && ($value instanceof Collection || $value instanceof Paginator)) {
                $records = $value instanceof Paginator ? $value->items() : $value->all();
            }
        }
        // Resolve display relations in batches; identifiers and source model fields stay intact.
        $displayRelations = [
            'bookings' => ['tourist', 'trip', 'currency', 'status'],
            'quotations' => ['tourist', 'currency', 'status', 'currentVersion.currency'],
            'invoices' => ['tourist', 'booking', 'currency', 'status'],
            'receipts' => ['invoice', 'currency', 'paymentMode'],
            'tourists' => ['country'], 'exchange_rates' => ['currency'],
            'trips' => ['tripType', 'tripStatus'], 'ratings' => ['tourist'],
            'testimonials' => ['tourist'], 'regions' => ['country'], 'districts' => ['region'],
            'bank_details' => ['bank', 'currency'], 'faqs' => ['faqCategory'],
            'users' => ['login.roles'],
        ];
        if ($records && isset($displayRelations[$slug])) {
            (new \Illuminate\Database\Eloquent\Collection($records))->loadMissing($displayRelations[$slug]);
        }
        // Render the existing forms server-side to retain their field names,
        // validation constraints, lookup options and action URLs during migration.
        // Only structured descriptors are returned; Vue never executes this HTML.
        $html = $view->render();

        return ['module' => $definition, 'records' => $records, 'details' => $details, 'forms' => self::forms($html, $definition['path']),
            'actions' => $slug === 'inquiries' && $id !== null ? [
                'can_create_quotation' => (bool) ($details['inquiry']?->is_approved && auth()->user()?->can('change-inquiries-status')),
            ] : [],
        ];
    }

    public static function forms(string $html, string $modulePath, ?string $recordId = null): array
    {
        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($dom);
        $forms = [];
        foreach ($xpath->query('//form') as $form) {
            $action = $form->getAttribute('action');
            $path = parse_url($action, PHP_URL_PATH) ?: '';
            if (! $path && $recordId) {
                $override = $xpath->query('.//input[@name="_method"]', $form)->item(0)?->getAttribute('value');
                if (in_array(strtoupper($override ?? ''), ['PUT', 'DELETE'], true)) {
                    $path = $modulePath.'/'.$recordId;
                }
            }
            if (! $path || in_array($path, ['/logout', '/login'])) {
                continue;
            }
            // The descriptor may only submit back to this application.
            $host = parse_url($action, PHP_URL_HOST);
            if ($host && ! in_array($host, [request()->getHost(), parse_url(config('app.url'), PHP_URL_HOST)], true)) {
                continue;
            }
            $fields = [];
            $method = strtoupper($form->getAttribute('method') ?: 'POST');
            foreach ($xpath->query('.//input | .//select | .//textarea', $form) as $input) {
                $name = $input->getAttribute('name');
                $type = $input->getAttribute('type') ?: $input->tagName;
                if (! $name || $name === '_token' || in_array($type, ['submit', 'button'])) {
                    continue;
                }
                if ($name === '_method') {
                    $method = strtoupper($input->getAttribute('value'));

                    continue;
                }
                $value = $input->tagName === 'textarea' ? $input->textContent : $input->getAttribute('value');
                $options = [];
                if ($input->tagName === 'select') {
                    $type = 'select';
                    $selected = [];
                    foreach ($input->getElementsByTagName('option') as $option) {
                        $options[] = ['value' => $option->getAttribute('value'), 'label' => trim($option->textContent)];
                        if ($option->hasAttribute('selected')) {
                            $selected[] = $option->getAttribute('value');
                        }
                    }
                    $value = $input->hasAttribute('multiple') ? $selected : ($selected[0] ?? ($options[0]['value'] ?? ''));
                }
                if ($type === 'checkbox') {
                    $value = $input->hasAttribute('checked');
                }
                $label = Str::headline(str_replace(['[]', '_id'], '', $name));
                $parent = $input->parentNode;
                for ($level = 0; $level < 3 && $parent; $level++, $parent = $parent->parentNode) {
                    $labels = $xpath->query('./label', $parent);
                    if ($labels->length) {
                        $label = trim($labels->item(0)->textContent);
                        break;
                    }
                }
                if ($type === 'checkbox' && str_ends_with($name, '[]')) {
                    $cells = $xpath->query('ancestor::tr[1]/td[1]', $input);
                    if ($cells->length) {
                        $label = trim($cells->item(0)->textContent);
                    }
                }
                if (preg_match('/^prices\[(\d+)\]\[(\d+)\]$/', $name, $matrix)) {
                    $season = Season::find($matrix[1]);
                    $class = ServiceClass::find($matrix[2]);
                    $label = ($season?->name ?? $matrix[1]).' / '.($class?->name ?? $matrix[2]);
                }
                if ($name === 'description' && $type === 'hidden') {
                    $type = 'richtext';
                }
                $fields[] = ['name' => $name, 'label' => trim(preg_replace('/\s*\*$/', '', $label)), 'type' => $type === 'input' ? 'text' : $type, 'value' => $value, 'optionValue' => $input->getAttribute('value'), 'step' => $input->getAttribute('step'), 'options' => $options, 'required' => $input->hasAttribute('required'), 'multiple' => $input->hasAttribute('multiple'), 'disabled' => $input->hasAttribute('disabled'), 'min' => $input->getAttribute('min'), 'max' => $input->getAttribute('max'), 'maxlength' => $input->getAttribute('maxlength'), 'placeholder' => $input->getAttribute('placeholder')];
            }
            // Describe repeatable table rows instead of relying on old JavaScript.
            $repeaterNames = [];
            foreach ($xpath->query('.//table//tbody//tr', $form) as $row) {
                $names = [];
                foreach ($xpath->query('.//input | .//select | .//textarea', $row) as $input) {
                    $name = $input->getAttribute('name');
                    if (str_ends_with($name, '[]') && ! $input->hasAttribute('multiple') && $input->getAttribute('type') !== 'checkbox') {
                        $names[] = $name;
                    }
                }
                if ($names) {
                    $repeaterNames[] = $names;
                }
            }
            $repeaters = [];
            $used = [];
            foreach ($repeaterNames as $names) {
                $key = implode('|', $names);
                if (isset($used[$key])) {
                    continue;
                }
                $used[$key] = true;
                $group = [];
                foreach ($names as $name) {
                    foreach ($fields as $field) {
                        if ($field['name'] === $name) {
                            $group[] = $field;
                            break;
                        }
                    }
                }
                $fields = array_values(array_filter($fields, fn ($f) => ! in_array($f['name'], $names, true)));
                $repeaters[] = ['label' => 'Items', 'fields' => $group];
            }
            // A checkbox list is a multi-select of IDs, not a single boolean.
            $checks = [];
            foreach ($fields as $field) {
                if ($field['type'] === 'checkbox' && str_ends_with($field['name'], '[]')) {
                    $checks[$field['name']][] = $field;
                }
            }
            foreach ($checks as $name => $items) {
                $fields = array_values(array_filter($fields, fn ($f) => $f['name'] !== $name));
                $field = $items[0];
                $field['type'] = 'select';
                $field['multiple'] = true;
                $field['options'] = array_map(fn ($f) => ['value' => $f['optionValue'], 'label' => $f['label']], $items);
                $field['value'] = array_column(array_filter($items, fn ($f) => $f['value']), 'optionValue');
                $fields[] = $field;
            }
            $title = '';
            $node = $form;
            for ($i = 0; $i < 5 && $node; $i++, $node = $node->parentNode) {
                $headings = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " modal-title ")]', $node);
                if ($headings->length) {
                    $title = trim($headings->item(0)->textContent);
                    break;
                }
                if ($node->nodeName === 'body') {
                    break;
                }
            }
            if (! $title) {
                $buttons = $xpath->query('.//button[@type="submit"]', $form);
                $title = $buttons->length ? trim($buttons->item(0)->textContent) : '';
                if (! $title) {
                    $title = $method === 'DELETE' ? 'Delete' : (str_contains($path, 'change_status') ? 'Change status' : 'Save changes');
                }
            }
            // Exclude search forms and the shared account menu.
            if ($method === 'GET') {
                continue;
            }
            preg_match('/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/i', $path, $recordMatch);
            $forms[] = ['recordKey' => $recordMatch[0] ?? null, 'title' => $title ?: 'Save changes', 'action' => $path, 'method' => $method, 'fields' => $fields, 'repeaters' => $repeaters];
        }

        return $forms;
    }
}
