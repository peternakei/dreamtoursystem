<?php

namespace App\Project\Modules\System\BankDetails\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditBankAccountDetailFormRequest extends FormRequest
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
            'bank' => 'required|integer',
            'currency' => 'required|integer',
            'account_name' => 'required',
            'account_number' => 'required',
        ];
    }
}
