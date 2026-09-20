<?php

namespace App\Project\Modules\System\Bookings;

use App\Project\Modules\System\Bookings\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\AgeGroups\AgeGroup;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Genders\Gender;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingTraveller extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'name',
        'phone',
        'email',
        'age_group_id',
        'gender_id',
        'birth_date',
        'country_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'uuid'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class, 'age_group_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'booking_status_id');
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
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
