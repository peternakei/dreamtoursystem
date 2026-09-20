<?php

namespace App\Project\Modules\System\Pages;

use Illuminate\Http\Request;

class ContentSupport
{
    public static function success(array $data, string $message = 'Retrieved successfully')
    {
        return response()->json(['status' => true, 'code' => 200, 'message' => $message, 'data' => $data]);
    }

    public static function localized(array $data, ?array $translations, string $locale): array
    {
        foreach (($translations[$locale] ?? []) as $key => $value) {
            if (array_key_exists($key, $data) && $value !== null && $value !== '') {
                $data[$key] = $value;
            }
        }

        return $data + ['locale' => $locale, 'fallback_locale' => 'en'];
    }

    public static function media($entity): array
    {
        return $entity->media->map(fn ($m) => ['uuid' => $m->uuid, 'id' => $m->id, 'url' => $m->url, 'role' => $m->role, 'title' => $m->title, 'sort_order' => $m->sort_order])->all();
    }

    public static function listing($query, Request $request, string $key, callable $resource)
    {
        $total = (clone $query)->count();
        $limit = $request->integer('limit', 20);
        $offset = $request->integer('offset', 0);

        return self::success([$key => $query->skip($offset)->take($limit)->get()->map($resource), 'total' => $total, 'limit' => $limit, 'offset' => $offset]);
    }

    public static function translationRules(array $fields): array
    {
        $rules = ['translations' => 'nullable|array:en,fr,sw'];
        foreach (['en', 'fr', 'sw'] as $locale) {
            $rules['translations.'.$locale] = 'nullable|array:'.implode(',', $fields);
            foreach ($fields as $field) {
                $rules['translations.'.$locale.'.'.$field] = 'nullable|string';
            }
        }

        return $rules;
    }
}
