<?php

namespace App\Project\Modules\System\Trips;

use App\Project\Modules\System\Trips\TripDayActivity;
use App\Project\Modules\System\Trips\Trip;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'day_number',
        'title',
        'destination_id',
        'accommodation_id',
        'accommodation_name',
        'accommodation_notes',
        'stay_type',
        'nights',
        'breakfast',
        'lunch',
        'dinner',
        'description',
        'sort_order',
        'created_by',
        'updated_by',
        'uuid',
    ];

    protected $casts = [
        'breakfast' => 'boolean',
        'lunch' => 'boolean',
        'dinner' => 'boolean',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }

    public function activities()
    {
        return $this->hasMany(TripDayActivity::class, 'trip_day_id')->orderBy('sort_order')->orderBy('id');
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
