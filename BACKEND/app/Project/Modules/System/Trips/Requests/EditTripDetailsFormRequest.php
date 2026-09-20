<?php

namespace App\Project\Modules\System\Trips\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditTripDetailsFormRequest extends FormRequest
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
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'type' => 'required|integer',
            'source' => 'required|integer',
            'last_booking_date' => 'required|date',
            'last_payment_date' => 'required|date',
            'trip_description' => 'required'
        ];
    }
}
