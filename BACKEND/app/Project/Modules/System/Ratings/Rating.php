<?php

namespace App\Project\Modules\System\Ratings;

use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tourist_id',
        'rateable_type',
        'rateable_id',
        'rateable_uuid',
        'rating',
        'comment',
        'created_at',
        'updated_at',
        'deleted_at',
        'uuid'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function tourist()
    {
        return $this->belongsTo(Tourist::class, 'tourist_id');
    }

    public function rateable()
    {
        return $this->morphTo();
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'rateable_id')->where('rateable_type', 'App\Project\Modules\System\Trips\Trip');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'rateable_id')->where('rateable_type', 'App\Project\Modules\System\Destinations\Destination');
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
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
