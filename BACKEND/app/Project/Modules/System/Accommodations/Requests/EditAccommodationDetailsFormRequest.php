<?php

namespace App\Project\Modules\System\Accommodations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditAccommodationDetailsFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accommodationUuid = $this->route('accommodation');

        return [
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('accommodations', 'name')->where(fn ($q) => $q->where('uuid', '!=', $accommodationUuid)),
            ],
            'stay_type_id' => 'nullable|integer|exists:stay_types,id',
            'primary_destination_id' => 'nullable|integer|exists:destinations,id',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'integer|exists:destinations,id',
            'description' => 'nullable|string',
            'location_text' => 'nullable|string|max:200',
        ];
    }
}
