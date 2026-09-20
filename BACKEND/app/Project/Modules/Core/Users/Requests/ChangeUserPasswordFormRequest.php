<?php

namespace App\Project\Modules\Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangeUserPasswordFormRequest extends FormRequest
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
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'password_confirm' => 'required|same:password'
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Enter a new password.',
            'password_confirm.required' => 'Confirm your new password.',
            'password_confirm.same' => 'The password confirmation does not match.',
        ];
    }
}
