<?php

namespace App\Project\Modules\System\Bookings;

use App\Project\Modules\System\Bookings\BookingType;
use App\Project\Modules\System\Bookings\BookingStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Invoices\Invoice;
use App\Project\Modules\System\Receipts\Receipt;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripGroup;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'tourist_id',
        'trip_id',
        'trip_group_id',
        'booking_type_id',
        'booking_date',
        'guest_count',
        'amount',
        'vat_amount',
        'total_amount',
        'currency_id',
        'remarks',
        'comments',
        'booking_status_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'uuid'
    ];

    public function tourist()
    {
        return $this->belongsTo(Tourist::class, 'tourist_id');
    }

    public function bookingType()
    {
        return $this->belongsTo(BookingType::class, 'booking_type_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function tripGroup()
    {
        return $this->belongsTo(TripGroup::class, 'trip_group_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(Receipt::class, Invoice::class, 'booking_id');
    }

    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'booking_status_id');
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
