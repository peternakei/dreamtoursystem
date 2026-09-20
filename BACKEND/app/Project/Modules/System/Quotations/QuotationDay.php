<?php

namespace App\Project\Modules\System\Quotations;

use App\Project\Modules\System\Quotations\QuotationDayActivity;
use App\Project\Modules\System\Quotations\QuotationVersion;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Trips\TripDay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuotationDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_version_id',
        'trip_day_id',
        'day_number',
        'travel_date',
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
        'travel_date' => 'date',
        'breakfast' => 'boolean',
        'lunch' => 'boolean',
        'dinner' => 'boolean',
    ];

    public function quotationVersion()
    {
        return $this->belongsTo(QuotationVersion::class, 'quotation_version_id');
    }

    public function tripDay()
    {
        return $this->belongsTo(TripDay::class, 'trip_day_id');
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
        return $this->hasMany(QuotationDayActivity::class, 'quotation_day_id')->orderBy('sort_order')->orderBy('id');
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
