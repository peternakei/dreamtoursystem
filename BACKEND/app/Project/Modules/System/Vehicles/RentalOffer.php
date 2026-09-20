<?php

namespace App\Project\Modules\System\Vehicles;

use App\Project\Modules\System\Library\HasLibraryMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RentalOffer extends Model
{
    use HasLibraryMedia, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['vehicle_uuids' => 'array', 'details' => 'array', 'translations' => 'array', 'is_active' => 'boolean', 'is_published' => 'boolean', 'is_featured' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(fn ($model) => $model->uuid = (string) Str::orderedUuid());
    }
}
