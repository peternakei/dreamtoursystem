<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Quotations\QuotationTerm;
use App\Project\Modules\System\Quotations\QuotationPriceLine;
use App\Project\Modules\System\Quotations\QuotationDay;
use App\Project\Modules\System\Quotations\QuotationShare;
use App\Project\Modules\System\Quotations\QuotationPaymentTerm;
use App\Project\Modules\System\Quotations\Quotation;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuotationVersion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'trip_id',
        'version_number',
        'reference_number',
        'title',
        'subtitle',
        'introduction',
        'highlights',
        'agent_intro_letter',
        'company_profile',
        'currency_id',
        'season_id',
        'service_class_id',
        'start_date',
        'end_date',
        'duration_days',
        'duration_nights',
        'guest_count',
        'amount',
        'vat_amount',
        'total_amount',
        'vat_enabled',
        'hide_price_breakdown',
        'hide_total_price',
        'hide_terms',
        'hide_payment_terms',
        'status',
        'public_token',
        'public_url_enabled',
        'public_expires_at',
        'pdf_path',
        'cover_image_path',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'booking_id',
        'internal_notes',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'public_expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'public_url_enabled' => 'boolean',
        'hide_price_breakdown' => 'boolean',
        'hide_total_price' => 'boolean',
        'hide_terms' => 'boolean',
        'hide_payment_terms' => 'boolean',
        'vat_enabled' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function serviceClass()
    {
        return $this->belongsTo(ServiceClass::class, 'service_class_id');
    }

    public function days()
    {
        return $this->hasMany(QuotationDay::class, 'quotation_version_id')->orderBy('sort_order')->orderBy('day_number');
    }

    public function priceLines()
    {
        return $this->hasMany(QuotationPriceLine::class, 'quotation_version_id')->orderBy('sort_order')->orderBy('id');
    }

    public function terms()
    {
        return $this->hasMany(QuotationTerm::class, 'quotation_version_id')->orderBy('type')->orderBy('sort_order')->orderBy('id');
    }

    public function paymentTerms()
    {
        return $this->hasMany(QuotationPaymentTerm::class, 'quotation_version_id')->orderBy('sort_order')->orderBy('id');
    }

    public function shares()
    {
        return $this->hasMany(QuotationShare::class, 'quotation_version_id')->latest();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
