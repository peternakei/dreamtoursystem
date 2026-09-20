<?php

namespace App\Project\Modules\System\Trips\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadTripBannerFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'banner' => 'required|mimes:jpg,jpeg,png,gif,webp,bmp|max:2024',
        ];
    }
}
