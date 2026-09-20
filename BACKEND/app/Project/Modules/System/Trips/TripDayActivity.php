<?php

namespace App\Project\Modules\System\Trips;

use App\Project\Modules\System\Trips\TripDay;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Activities\Activity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripDayActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_day_id',
        'activity_id',
        'title',
        'description',
        'is_optional',
        'sort_order',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
    ];

    public function tripDay()
    {
        return $this->belongsTo(TripDay::class, 'trip_day_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
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
