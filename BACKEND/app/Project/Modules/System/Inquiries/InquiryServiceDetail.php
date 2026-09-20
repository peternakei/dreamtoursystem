<?php

namespace App\Project\Modules\System\Inquiries;

use Illuminate\Database\Eloquent\Model;

class InquiryServiceDetail extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['contact' => 'array', 'request_details' => 'array', 'offer_snapshot' => 'array', 'operations' => 'array', 'agreed_price' => 'array'];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }
}
