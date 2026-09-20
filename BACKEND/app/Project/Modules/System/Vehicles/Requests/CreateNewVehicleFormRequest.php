<?php

namespace App\Project\Modules\System\Vehicles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewVehicleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150|unique:vehicles,name',
            'capacity' => 'nullable|string|max:80',
            'description' => 'nullable|string',
        ];
    }
}
