<?php

namespace App\Project\Auth\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationFormRequest extends FormRequest
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
            'phone' => 'required|unique:tourists,phone',
            'email' => 'required',
            'country' => 'required|integer',
            'address' => 'required',
            'gender' => 'required|integer',
            'password' => 'required|min:6',
            'confirm-password' => 'required',
            'passport_image' => 'nullable|file|mimes:png,jpg,jpeg,gif,webp,bmp|max:2042',
        ];
    }
}
