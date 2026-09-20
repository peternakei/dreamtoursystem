<?php

namespace App\Project\Modules\System\Likes\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ToggleLikeRequest extends FormRequest
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
            'likeable_type' => 'required|in:trip,destination',
            'likeable_uuid' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'likeable_type.in' => 'Likeable type must be either trip or destination.',
            'likeable_uuid.required' => 'Likeable UUID is required.',
        ];
    }
}
