<?php

namespace App\Project\Auth\Services\Api;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Library\Services\RasterUploadNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserRegistrationFormAction
{
    public function __construct(protected RasterUploadNormalizer $rasterUploadNormalizer)
    {
    }

    public function handle(Request $request)
    {
        DB::beginTransaction();

        //check email
        $exists = Tourist::where('email', $request->email)->get();
        if (count($exists) > 0) {
            return response()->json([
                'status' => "error",
                'code' => 100,
                'message' => 'Email already exists'
            ]);
        }

        //save
        $save = Tourist::create([
            'name' => htmlspecialchars($request->name),
            'gender_id' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'country_id' => $request->country,
            'address' => $request->address,
        ]);

        if (!$save) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to save user details'
            ]);
        }

        //save logins
        $logins = $this->saveLogins($save, $request);
        if (!$logins) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to save user details'
            ]);
        }

        //upload passport image
        $upload = $this->uploadPassportImage($save,$logins, $request);
        if (!$upload) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to save user details'
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Tourist registered successfully',
            'data' => [
                'user' => [
                    'id' => $save->id,
                    'name' => $save->name,
                    'tourist_number' => $save->tourist_number,
                    'email' => $save->email
                ]
            ]
        ]);
    }

    private function saveLogins($user, $request)
    {
        return User::create([
            'username' => $user->email,
            'password' => bcrypt($request->password),
            'profile' => 'Tourist',
            'profile_id' => $user->id,
        ]);
    }

    private function uploadPassportImage($user,$login, $request)
    {
        $files = $request->passport_image;
        $attachment_types = [1];

        if (!is_null($files)) {
            $normalized = $this->rasterUploadNormalizer->normalize($request->passport_image);
            $fileName = hrtime(true) . '.' . $normalized['extension'];
            $fileSize = $normalized['size'];

            $filePath = 'uploads/' . $fileName;

            $path = Storage::disk('attachments')->put($filePath, $normalized['binary']);
            $path = Storage::url($fileName);

            $saveImage = $user->attachments()->create([
                'name' => $fileName,
                'path' => $path,
                'size' => $fileSize,
                'attachment_type_id' => $attachment_types[0],
                'created_by' => $login->id
            ]);

            if (!$saveImage) {
                return false;
            }
        }

        return true;
    }
}
