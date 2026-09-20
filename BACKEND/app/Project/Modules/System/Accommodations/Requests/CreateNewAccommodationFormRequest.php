<?php

namespace App\Project\Modules\System\Accommodations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewAccommodationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200|unique:accommodations,name',
            'stay_type_id' => 'nullable|integer|exists:stay_types,id',
            'primary_destination_id' => 'nullable|integer|exists:destinations,id',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'integer|exists:destinations,id',
            'description' => 'nullable|string',
            'location_text' => 'nullable|string|max:200',
        ];
    }
}
