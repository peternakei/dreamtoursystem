<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Quotations\QuotationVersion;

use App\Project\Modules\Core\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuotationShare extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_version_id',
        'sent_to',
        'sent_from',
        'subject',
        'message',
        'channel',
        'status',
        'sent_at',
        'opened_at',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
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
