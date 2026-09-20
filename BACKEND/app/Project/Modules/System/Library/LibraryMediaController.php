<?php

namespace App\Project\Modules\System\Library;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\Attachments\AttachmentType;
use App\Project\Modules\System\Library\Services\RasterUploadNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LibraryMediaController extends Controller
{
    public function __construct(protected RasterUploadNormalizer $rasterUploadNormalizer)
    {
    }

    public function store(Request $request, string $type, string $uuid)
    {
        $entity = $this->resolveEntity($type, $uuid);
        $role = $this->validateRole($request->input('role', Attachment::ROLE_GALLERY));

        $rules = [
            'role' => ['nullable', 'string'],
            'title' => ['nullable', 'string', 'max:150'],
            'files' => ['required', 'array', 'min:1'],
        ];

        if ($role === Attachment::ROLE_VIDEO) {
            $rules['files.*'] = [
                'file',
                'mimes:' . config('library.allowed_video_mimes'),
                'max:' . (config('library.max_video_mb') * 1024),
            ];
        } else {
            $rules['files.*'] = [
                'file',
                'mimes:' . config('library.allowed_image_mimes'),
                'max:' . (config('library.max_image_mb') * 1024),
            ];
        }

        $request->validate($rules);

        $created = [];
        $title = $request->input('title');
        $typeId = $this->resolveAttachmentTypeId();

        DB::beginTransaction();
        try {
            $maxSort = $entity->media()->where('role', $role)->max('sort_order') ?? -1;

            foreach ($request->file('files') as $i => $uploaded) {
                if ($role === Attachment::ROLE_VIDEO) {
                    $stored = $this->storeRawUpload($uploaded, $i);
                } else {
                    $stored = $this->storeNormalizedImage($uploaded, $i);
                }

                $created[] = $entity->media()->create([
                    'name' => $stored['name'],
                    'path' => $stored['url'],
                    'attachment_type_id' => $typeId,
                    'role' => $role,
                    'title' => $title,
                    'sort_order' => ++$maxSort,
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => count($created) . ' file(s) uploaded',
            'data' => $created,
        ]);
    }

    public function update(Request $request, int $attachment)
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
        ]);

        $media = Attachment::findOrFail($attachment);
        $media->update([
            'title' => $request->input('title'),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'data' => $media]);
    }

    public function destroy(int $attachment)
    {
        $media = Attachment::findOrFail($attachment);
        $media->update(['updated_by' => Auth::id()]);
        $media->delete();

        return response()->json(['status' => true]);
    }

    public function updateDescription(Request $request, string $type, string $uuid)
    {
        $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        $entity = $this->resolveEntity($type, $uuid);
        $entity->description = $request->input('description');
        $entity->updated_by = Auth::id();
        $entity->save();

        return response()->json([
            'status' => true,
            'data' => ['description' => $entity->description],
        ]);
    }

    public function reorder(Request $request, string $type, string $uuid)
    {
        $request->validate([
            'role' => ['required', 'string'],
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        $entity = $this->resolveEntity($type, $uuid);
        $role = $this->validateRole($request->input('role'));

        $ids = $request->input('order');
        $existing = $entity->media()
            ->where('role', $role)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($existing, $ids) {
            foreach ($ids as $position => $id) {
                if (in_array($id, $existing, true)) {
                    Attachment::where('id', $id)->update(['sort_order' => $position]);
                }
            }
        });

        return response()->json(['status' => true]);
    }

    protected function resolveEntity(string $type, string $uuid): Model
    {
        $map = config('library.entities', []);
        if (!isset($map[$type])) {
            abort(404, "Unknown library entity: {$type}");
        }

        $modelClass = $map[$type];
        $entity = $modelClass::where('uuid', $uuid)->first();
        if (!$entity) {
            abort(404, ucfirst($type) . ' not found');
        }

        if (!method_exists($entity, 'media')) {
            abort(500, $modelClass . ' does not use HasLibraryMedia trait');
        }

        return $entity;
    }

    protected function validateRole(string $role): string
    {
        if (!in_array($role, config('library.roles', []), true)) {
            abort(422, "Invalid media role: {$role}");
        }

        return $role;
    }

    protected function resolveAttachmentTypeId(): int
    {
        return AttachmentType::firstOrCreate(
            ['name' => config('library.attachment_type_name')],
            ['color' => config('library.attachment_type_color')]
        )->id;
    }

    protected function storeNormalizedImage($uploaded, int $index): array
    {
        $normalized = $this->rasterUploadNormalizer->normalize($uploaded);
        $fileName = hrtime(true) . '_' . $index . '.' . $normalized['extension'];

        Storage::disk('attachments')->put('uploads/' . $fileName, $normalized['binary']);

        return [
            'name' => $fileName,
            'url' => Storage::url($fileName),
        ];
    }

    protected function storeRawUpload($uploaded, int $index): array
    {
        $ext = strtolower($uploaded->getClientOriginalExtension() ?: 'bin');
        $fileName = hrtime(true) . '_' . $index . '.' . $ext;

        Storage::disk('attachments')->put('uploads/' . $fileName, file_get_contents($uploaded->getRealPath()));

        return [
            'name' => $fileName,
            'url' => Storage::url($fileName),
        ];
    }
}
