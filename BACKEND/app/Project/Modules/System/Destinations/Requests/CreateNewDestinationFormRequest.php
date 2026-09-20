<?php

namespace App\Project\Modules\System\Destinations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewDestinationFormRequest extends FormRequest
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
            'name' => 'required|unique:destinations,name',
            'location' => 'required|integer',
            'region' => 'required|integer',
            'latitude' => 'required',
            'longitude' => 'required',
            'description' => 'required',
        ];
    }
}
