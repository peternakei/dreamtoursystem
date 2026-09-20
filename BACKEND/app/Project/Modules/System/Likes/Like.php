<?php

namespace App\Project\Modules\System\Likes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Support\Str;

class Like extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tourist_id',
        'likeable_type',
        'likeable_id',
        'likeable_uuid',
        'created_at',
        'updated_at',
        'deleted_at',
        'uuid'
    ];

    public function tourist()
    {
        return $this->belongsTo(Tourist::class, 'tourist_id');
    }

    public function likeable()
    {
        return $this->morphTo();
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'likeable_id')->where('likeable_type', 'App\Project\Modules\System\Trips\Trip');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'likeable_id')->where('likeable_type', 'App\Project\Modules\System\Destinations\Destination');
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
