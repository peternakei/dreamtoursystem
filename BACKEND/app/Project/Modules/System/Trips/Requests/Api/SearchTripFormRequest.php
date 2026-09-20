<?php

namespace App\Project\Modules\System\Trips\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchTripFormRequest extends FormRequest
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
            'location' => 'required|integer',
            'guest_count' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'category_uuid' => 'nullable',
            'category_id' => 'nullable',
        ];
    }
}
