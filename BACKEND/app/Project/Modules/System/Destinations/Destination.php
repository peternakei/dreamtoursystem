<?php

namespace App\Project\Modules\System\Destinations;

use App\Project\Modules\System\Destinations\DestinationFact;
use App\Project\Modules\System\Destinations\DestinationCategory;
use App\Project\Modules\System\Destinations\DestinationActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Library\HasLibraryMedia;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\Core\Locations\Location;
use App\Project\Modules\Core\Regions\Region;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory, SoftDeletes, HasLibraryMedia;

    protected $fillable = [
        'name',
        'location_id',
        'latitude',
        'longitude',
        'region_id',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'is_active',
        'uuid'
    ];

    public function images()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function facts()
    {
        return $this->hasMany(DestinationFact::class, 'destination_id');
    }

    public function activities()
    {
        return $this->hasMany(DestinationActivity::class, 'destination_id');
    }

    public function categories()
    {
        return $this->hasMany(DestinationCategory::class, 'destination_id');
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
