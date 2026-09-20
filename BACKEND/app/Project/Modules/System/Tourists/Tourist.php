<?php

namespace App\Project\Modules\System\Tourists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Genders\Gender;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Tourist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tourist_number',
        'name',
        'gender_id',
        'phone',
        'email',
        'country_id',
        'address',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'is_active',
        'uuid'
    ];

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function passport()
    {
        return Attachment::where(['attachment_type_id' => 1, 'attachmentable_id' => $this->id,'attachmentable_type' => 'App\Project\Modules\System\Tourists\Tourist']);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function trips()
    {
        return $this->hasMany(Booking::class, 'tourist_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
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
