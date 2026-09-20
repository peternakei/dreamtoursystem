<?php

namespace App\Project\Modules\System\Destinations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDestinationFactFormRequest extends FormRequest
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
            'fact' => 'required',
            'sub_fact' => 'nullable',
            'description' => 'required',
        ];
    }
}
