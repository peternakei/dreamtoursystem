<?php

namespace App\Project\Modules\System\Destinations\Services;

use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationActivity;
use App\Project\Modules\System\Library\Services\RasterUploadNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignDestinationActivityFormAction
{
    public function __construct(protected RasterUploadNormalizer $rasterUploadNormalizer) {}

    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $destination = Destination::where('uuid', $id)->first();

        for ($i = 0; $i < count($request->activity); $i++) {

            // save destination activity
            $save = DestinationActivity::create([
                'destination_id' => $destination->id,
                'activity_id' => $request->activity[$i],
                'created_by' => Auth::user()->id,
            ]);

            if (! $save) {
                DB::rollBack();

                return ['status' => false, 'message' => 'Failed to save destination activity'];
            }

            $saveImage = $this->saveDestinationActivityImage($save, $request->file('activity_image.'.$i));
            if (! $saveImage['status']) {
                DB::rollBack();

                return ['status' => false, 'message' => 'Failed to upload destination image details'];
            }
        }

        DB::commit();

        return ['status' => true, 'message' => 'Activity assigned successfully.'];
    }

    private function saveDestinationActivityImage($destinationActivity, $image)
    {
        $files = $image;
        $attachment_types = [5];

        if (! is_null($files)) {
            $normalized = $this->rasterUploadNormalizer->normalize($image);
            $fileName = hrtime(true).'.'.$normalized['extension'];
            $fileSize = $normalized['size'];

            $filePath = 'uploads/'.$fileName;

            $path = Storage::disk('attachments')->put($filePath, $normalized['binary']);
            $path = Storage::url($fileName);

            $saveImage = $destinationActivity->images()->create([
                'name' => $fileName,
                'path' => $path,
                'size' => $fileSize,
                'attachment_type_id' => $attachment_types[0],
                'created_by' => Auth::user()->id,
            ]);

            if (! $saveImage) {
                return ['status' => false, 'message' => 'Failed to create destination activity image.'];
            }
        }

        return ['status' => true, 'message' => 'Destination image uploaded successfully.'];
    }
}
