<?php

namespace App\Project\Modules\System\Inquiries;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Quotations\Quotation;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\TripType;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Destinations\Destination;
use App\Models\Web\System\Location\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inquiry_code',
        'request_reference',
        'tourist_id',
        'description',
        'tour_title',
        'from_date',
        'to_date',
        'trip_type_id',
        'destinations',
        'locations',
        'service_class_id',
        'guests',
        'budget',
        'status',
        'source',
        'assigned_to',
        'communication_language',
        'received_at',
        'is_approved',
        'comments',
        'client_message',
        'user_id',
        'created_by',
        'updated_by',
        'uuid'
    ];

    protected $casts = [
        'destinations' => 'array',
        'locations' => 'array',
        'from_date' => 'date',
        'to_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function tourist()
    {
        return $this->belongsTo(Tourist::class, 'tourist_id');
    }

    public function tripType()
    {
        return $this->belongsTo(TripType::class, 'trip_type_id');
    }

    public function serviceClass()
    {
        return $this->belongsTo(ServiceClass::class, 'service_class_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'inquiry_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
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
            $model->is_approved = false;
            $model->status = $model->status ?? 'pending';
            $model->inquiry_code = 'INQ-' . strtoupper(Str::random(8));
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
