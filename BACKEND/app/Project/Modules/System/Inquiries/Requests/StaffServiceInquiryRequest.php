<?php

namespace App\Project\Modules\System\Inquiries\Requests;

use App\Project\Modules\System\Inquiries\Requests\Api\CreateServiceInquiryRequest;
use App\Project\Modules\System\Inquiries\ServiceAccess;
use Illuminate\Validation\Rule;

class StaffServiceInquiryRequest extends CreateServiceInquiryRequest
{
    public function authorize(): bool
    {
        return ServiceAccess::allowed($this->user());
    }

    public function rules(): array
    {
        return parent::rules() + [
            'source' => 'required|in:phone,whatsapp,walk_in,email,office',
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where('profile', 'SystemUser')->where('is_active', true)],
            'staff_notes' => 'nullable|string|max:10000',
        ];
    }
}
