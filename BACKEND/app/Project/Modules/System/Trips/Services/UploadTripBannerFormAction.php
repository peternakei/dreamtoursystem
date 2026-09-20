<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Library\Services\RasterUploadNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadTripBannerFormAction
{
    public function __construct(protected RasterUploadNormalizer $rasterUploadNormalizer)
    {
    }

    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();

        $saveImage = $this->saveTripBanner($trip, $request);
        if (!$saveImage['status']) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to upload banner trip details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Trip banner uploaded successfully'];
    }

    private function saveTripBanner($trip, $request)
    {
        $files = $request->banner;
        $attachment_types = [8];

        if (!is_null($files)) {
            $normalized = $this->rasterUploadNormalizer->normalize($request->banner);
            $fileName = hrtime(true) . '.' . $normalized['extension'];
            $fileSize = $normalized['size'];

            $filePath = 'uploads/' . $fileName;

            $path = Storage::disk('attachments')->put($filePath, $normalized['binary']);
            $path = Storage::url($fileName);

            $saveImage = $trip->banners()->create([
                'name' => $fileName,
                'path' => $path,
                'size' => $fileSize,
                'attachment_type_id' => $attachment_types[0],
                'created_by' => Auth::user()->id
            ]);

            if (!$saveImage) {
                return ['status' => false, 'message' => 'Failed to create trip image.'];
            }
        }

        return ['status' => true, 'message' => 'Trip image uploaded successfully.'];
    }
}
