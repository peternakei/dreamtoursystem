<?php

namespace App\Project\Modules\System\Tourists\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewTouristFormRequest extends FormRequest
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
            'name' => 'required',
            'gender' => 'required|integer',
            'address' => 'nullable',
            'phone' => 'required|unique:tourists,phone',
            'email' => 'required|unique:tourists,email',
            'country' => 'required|integer'
        ];
    }
}
