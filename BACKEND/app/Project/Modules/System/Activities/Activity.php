<?php

namespace App\Project\Modules\System\Activities;

use App\Project\Modules\System\Activities\ActivityPrice;

use App\Project\Modules\System\Library\HasLibraryMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory, HasLibraryMedia;

    protected $fillable = [
        'name',
        'description',
        'color',
        'sort_order',
        'is_active',
        'uuid',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    public function prices()
    {
        return $this->hasMany(ActivityPrice::class, 'activity_id')->orderBy('created_at','desc');
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = (new Carbon($value))->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->attributes['updated_at'] = (new Carbon($value))->toDateTimeString();
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
