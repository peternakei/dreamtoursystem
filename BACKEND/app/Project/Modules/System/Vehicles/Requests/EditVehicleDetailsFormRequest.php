<?php

namespace App\Project\Modules\System\Vehicles\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditVehicleDetailsFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('vehicles', 'name')->ignore($vehicleId),
            ],
            'capacity' => 'nullable|string|max:80',
            'description' => 'nullable|string',
        ];
    }
}
