<?php

namespace App\Project\Modules\System\Categories\Services\Api;

use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\Trips\Category;
use Illuminate\Http\Request;

class GetCategoriesFormAction
{
    public function handle(Request $request)
    {
        $limit = $request->integer('limit');
        $offset = max($request->integer('offset', 0), 0);

        $query = Category::query()
            ->where('is_active', true)
            ->with([
                'media' => function ($query) {
                    $query->select([
                        'id',
                        'uuid',
                        'name',
                        'path',
                        'role',
                        'title',
                        'sort_order',
                        'attachmentable_id',
                        'attachmentable_type',
                    ]);
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($offset > 0) {
            $query->skip($offset);
        }

        if ($limit !== null && $limit > 0) {
            $query->take($limit);
        }

        $categories = $query->get();

        $data = $categories->map(function (Category $category) {
            $imageMedia = $category->media
                ->whereIn('role', [Attachment::ROLE_GALLERY, Attachment::ROLE_COVER])
                ->values();

            $cover = $category->media->firstWhere('role', Attachment::ROLE_COVER) ?? $imageMedia->first();
            $video = $category->media->firstWhere('role', Attachment::ROLE_VIDEO);

            return [
                'id' => $category->id,
                'uuid' => $category->uuid,
                'name' => $category->name,
                'description' => $category->description,
                'color' => $category->color,
                'is_active' => (bool) $category->is_active,
                'created_at' => $category->created_at,
                'image' => $cover?->url ?? '',
                'images' => $imageMedia
                    ->map(fn (Attachment $media) => $this->transformMedia($media))
                    ->values()
                    ->all(),
                'cover' => $cover ? $this->transformMedia($cover) : null,
                'video' => $video ? $this->transformMedia($video) : null,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Categories retrieved successfully',
            'data' => [
                'categories' => $data,
                'returned_count' => $data->count(),
            ],
        ]);
    }

    protected function transformMedia(Attachment $media): array
    {
        return [
            'id' => $media->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'title' => $media->title,
            'role' => $media->role,
            'sort_order' => $media->sort_order,
            'url' => $media->url,
        ];
    }
}
