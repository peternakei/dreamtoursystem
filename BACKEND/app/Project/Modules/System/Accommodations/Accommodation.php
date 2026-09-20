<?php

namespace App\Project\Modules\System\Accommodations;

use App\Project\Modules\System\Accommodations\StayType;

use App\Project\Modules\System\Library\HasLibraryMedia;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes, HasLibraryMedia;

    protected $fillable = [
        'uuid',
        'name',
        'stay_type_id',
        'primary_destination_id',
        'description',
        'location_text',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    public function stayType()
    {
        return $this->belongsTo(StayType::class, 'stay_type_id');
    }

    public function primaryDestination()
    {
        return $this->belongsTo(Destination::class, 'primary_destination_id');
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'accommodation_destination')
            ->withPivot(['is_primary', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
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
            $model->uuid = $model->uuid ?: (string) Str::orderedUuid();
            if (is_null($model->is_active)) {
                $model->is_active = true;
            }
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
