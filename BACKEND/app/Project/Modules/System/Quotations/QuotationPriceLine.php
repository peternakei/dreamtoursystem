<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Quotations\QuotationVersion;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Currencies\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuotationPriceLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_version_id',
        'source_type',
        'source_id',
        'description',
        'traveler_type',
        'quantity',
        'unit_price',
        'total_price',
        'currency_id',
        'is_optional',
        'is_visible',
        'sort_order',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function quotationVersion()
    {
        return $this->belongsTo(QuotationVersion::class, 'quotation_version_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
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
