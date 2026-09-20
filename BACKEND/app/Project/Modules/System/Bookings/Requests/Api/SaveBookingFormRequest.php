<?php

namespace App\Project\Modules\System\Bookings\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SaveBookingFormRequest extends FormRequest
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
            'trip_uuid' => 'required',
            'class_id' => 'required|integer',
            'guest_count' => 'required',
            'gender' => 'nullable',
            'country' => 'nullable',
            'start_date' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'fname' => 'required',
            'lname' => 'required',
            'note' => 'required',
        ];
    }
}
