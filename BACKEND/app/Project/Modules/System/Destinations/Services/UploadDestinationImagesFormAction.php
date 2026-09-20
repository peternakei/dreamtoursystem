<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Library\Services\RasterUploadNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadDestinationImagesFormAction
{
    public function __construct(protected RasterUploadNormalizer $rasterUploadNormalizer)
    {
    }

    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $destination = Destination::where('uuid', $id)->first();

        $saveImage = $this->saveDestinationImage($destination, $request);
        if (!$saveImage['status']) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to upload identity image details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Identities uploaded successfully'];
    }

    private function saveDestinationImage($destination, $request)
    {
        $files = $request->identity_image;
        $attachment_types = [4];

        for ($i = 0; $i < count($files); $i++) {

            if (!is_null($files)) {
                $uploaded = $request->identity_image[$i];
                $normalized = $this->rasterUploadNormalizer->normalize($uploaded);
                $fileName = hrtime(true) . '_' . $i . '.' . $normalized['extension'];
                $fileSize = $normalized['size'];

                $filePath = 'uploads/' . $fileName;

                $path = Storage::disk('attachments')->put($filePath, $normalized['binary']);
                $path = Storage::url($fileName);

                $saveImage = $destination->images()->create([
                    'name' => $fileName,
                    'path' => $path,
                    'size' => $fileSize,
                    'attachment_type_id' => $attachment_types[0],
                    'created_by' => Auth::user()->id
                ]);

                if (!$saveImage) {
                    return ['status' => false, 'message' => 'Failed to create destination image.'];
                }
            }
        }

        return ['status' => true, 'message' => 'Destination image uploaded successfully.'];
    }
}
