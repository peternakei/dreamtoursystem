<?php

namespace App\Project\Modules\System\Trips;

use App\Project\Modules\System\Trips\TripGroupCamp;
use App\Project\Modules\System\Trips\Trip;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Bookings\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'group',
        'size',
        'color',
        'departure_date',
        'days',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'is_active',
        'uuid'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function camps()
    {
        return $this->hasMany(TripGroupCamp::class, 'trip_group_id');
    }

    public function availability()
    {
        $booked = Booking::where(['trip_id' => $this->trip_id, 'trip_group_id' => $this->id])->whereIn('booking_status_id', [1, 2, 3])->sum('guest_count');

        return (int)$this->size - (int)$booked;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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
            $model->is_active = true;
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
