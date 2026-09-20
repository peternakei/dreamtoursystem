<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Quotations\QuotationDay;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Trips\TripDayActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuotationDayActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_day_id',
        'trip_day_activity_id',
        'activity_id',
        'title',
        'description',
        'is_optional',
        'price',
        'currency_id',
        'sort_order',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
    ];

    public function quotationDay()
    {
        return $this->belongsTo(QuotationDay::class, 'quotation_day_id');
    }

    public function tripDayActivity()
    {
        return $this->belongsTo(TripDayActivity::class, 'trip_day_activity_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
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
