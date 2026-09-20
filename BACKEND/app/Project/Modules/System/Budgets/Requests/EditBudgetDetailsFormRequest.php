<?php

namespace App\Project\Modules\System\Budgets\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditBudgetDetailsFormRequest extends FormRequest
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
            'trip' => 'required|integer',
            'season' => 'required|integer',
            'class' => 'required|integer',
            'currency' => 'required|integer',
            'quantity' => 'required',
            'price' => 'required'
        ];
    }
}
