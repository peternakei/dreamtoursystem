<?php

namespace App\Project\Modules\System\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateActivityPriceFormRequest extends FormRequest
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
            'age' => 'required|integer',
            'type' => 'required|integer',
            'currency' => 'required|integer',
            'price' => 'required',
            'duration' => 'required'
        ];
    }
}
