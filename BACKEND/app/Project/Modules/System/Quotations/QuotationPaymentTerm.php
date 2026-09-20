<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Quotations\QuotationVersion;

use App\Project\Modules\Core\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuotationPaymentTerm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_version_id',
        'title',
        'description',
        'is_visible',
        'sort_order',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function quotationVersion()
    {
        return $this->belongsTo(QuotationVersion::class, 'quotation_version_id');
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
