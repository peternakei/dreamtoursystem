<?php

namespace App\Project\Modules\System\Inquiries\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewInquiryFormRequest extends FormRequest
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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'required|integer|exists:countries,id',
            'tripType' => 'required|integer|exists:trip_types,id',
            'destinations' => 'nullable|array',
            'destinations.*' => 'integer|exists:destinations,id',
            'locations' => 'nullable|array',
            'locations.*' => 'integer|exists:locations,id',
            'serviceClass' => 'nullable|integer|exists:service_classes,id',
            'guests' => 'required|integer|min:1',
            'startDate' => 'required|date|after:today',
            'endDate' => 'required|date|after:startDate',
            'budget' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'startDate.after' => 'Start date must be in the future',
            'endDate.after' => 'End date must be after start date',
        ];
    }
}
