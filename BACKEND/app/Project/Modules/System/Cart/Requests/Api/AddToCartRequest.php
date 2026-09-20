<?php

namespace App\Project\Modules\System\Cart\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
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
            'cartable_type' => 'required|in:trip,destination',
            'cartable_uuid' => 'required|string',
            'quantity' => 'nullable|integer|min:1|max:10',
            'start_date' => 'nullable|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'cartable_type.in' => 'Cartable type must be either trip or destination.',
            'cartable_uuid.required' => 'Cartable UUID is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 10.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.after' => 'End date must be after start date.',
        ];
    }
}
