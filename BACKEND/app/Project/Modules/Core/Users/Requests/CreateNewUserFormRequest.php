<?php

namespace App\Project\Modules\Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewUserFormRequest extends FormRequest
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
            'phone' => 'required|numeric|min:10|unique:system_users,phone',
            'email' => 'required|email|unique:system_users,email',
            'role' => 'required'
        ];
    }
}
