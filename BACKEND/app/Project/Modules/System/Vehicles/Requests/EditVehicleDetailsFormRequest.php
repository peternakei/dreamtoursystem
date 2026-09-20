<?php

namespace App\Project\Modules\System\Vehicles\Requests;

use App\Project\Modules\System\Vehicles\Vehicle;
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
        $vehicle = Vehicle::where('uuid', $this->route('vehicle'))->firstOrFail();

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('vehicles', 'name')->ignore($vehicle),
            ],
            'capacity' => 'nullable|string|max:80',
            'description' => 'nullable|string',
        ];
    }
}
