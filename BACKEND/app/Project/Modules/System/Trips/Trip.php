<?php

namespace App\Project\Modules\System\Trips;

use App\Project\Modules\System\Trips\TripAddon;
use App\Project\Modules\System\Trips\TripPrice;
use App\Project\Modules\System\Trips\TripCategory;
use App\Project\Modules\System\Trips\TripSource;
use App\Project\Modules\System\Trips\TripCategoryActivity;
use App\Project\Modules\System\Trips\TripGroup;
use App\Project\Modules\System\Trips\TripDay;
use App\Project\Modules\System\Trips\TripStatus;
use App\Project\Modules\System\Trips\TripGroupCamp;
use App\Project\Modules\System\Trips\TripDestination;
use App\Project\Modules\System\Trips\TripType;
use App\Project\Modules\System\Trips\TripPoint;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\Seasons\Budget;
use App\Project\Modules\System\Quotations\QuotationVersion;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_code',
        'name',
        'slug',
        'description',
        'from_date',
        'to_date',
        'duration_days',
        'duration_nights',
        'last_booking_date',
        'last_payment_date',
        'trip_type_id',
        'trip_source_id',
        'trip_status_id',
        'is_published',
        'publish_remarks',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'is_active',
        'uuid'
    ];

    public function banners()
    {
        return $this->morphMany(Attachment::class, 'attachmentable')->where('is_active', true);
    }

    public function tripType()
    {
        return $this->belongsTo(TripType::class, 'trip_type_id');
    }
    public function tripSource()
    {
        return $this->belongsTo(TripSource::class, 'trip_source_id');
    }
    public function tripStatus()
    {
        return $this->belongsTo(TripStatus::class, 'trip_status_id');
    }

    public function addons()
    {
        return $this->hasMany(TripAddon::class, 'trip_id');
    }

    public function categories()
    {
        return $this->hasMany(TripCategory::class, 'trip_id');
    }

    public function categoryActivities()
    {
        return $this->hasManyThrough(TripCategoryActivity::class, TripCategory::class, 'trip_id');
    }

    public function destinations()
    {
        return $this->hasMany(TripDestination::class, 'trip_id');
    }

    public function groups()
    {
        return $this->hasMany(TripGroup::class, 'trip_id');
    }

    public function groupCamps()
    {
        return $this->hasManyThrough(TripGroupCamp::class, TripGroup::class, 'trip_id');
    }

    public function points()
    {
        return $this->hasMany(TripPoint::class, 'trip_id');
    }

    public function tripDays()
    {
        return $this->hasMany(TripDay::class, 'trip_id')->orderBy('sort_order')->orderBy('day_number');
    }

    public function prices()
    {
        return $this->hasMany(TripPrice::class, 'trip_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'trip_id');
    }

    public function quotationVersions()
    {
        return $this->hasMany(QuotationVersion::class, 'trip_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calendar days for display when duration_days is unset or zero.
     */
    public function effectiveDurationDays(): int
    {
        $d = (int) ($this->duration_days ?? 0);
        if ($d > 0) {
            return $d;
        }
        if ($this->from_date && $this->to_date) {
            return (int) max(
                Carbon::parse($this->from_date)->startOfDay()->diffInDays(Carbon::parse($this->to_date)->startOfDay()) + 1,
                1
            );
        }

        return 0;
    }

    /**
     * Nights for display when duration_nights is null (inferred from effective days).
     */
    public function effectiveDurationNights(): int
    {
        if ($this->duration_nights !== null) {
            return (int) $this->duration_nights;
        }

        $days = $this->effectiveDurationDays();

        return $days > 0 ? max($days - 1, 0) : 0;
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = (new Carbon($value))->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->attributes['updated_at'] = (new Carbon($value))->toDateTimeString();
    }

    public function getDeletedAtAttribute($value)
    {
        return $this->attributes['deleted_at'] = (new Carbon($value))->toDateTimeString();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->is_published = $model->is_published ?? false;
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
