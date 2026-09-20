<?php

namespace App\Project\Modules\System\Attachments;

use App\Project\Modules\System\Attachments\AttachmentType;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    public const ROLE_GALLERY = 'gallery';
    public const ROLE_COVER = 'cover';
    public const ROLE_VIDEO = 'video';

    public const ROLES = [
        self::ROLE_GALLERY,
        self::ROLE_COVER,
        self::ROLE_VIDEO,
    ];

    protected $fillable = [
        'name',
        'path',
        'attachment_type_id',
        'role',
        'title',
        'sort_order',
        'attachmentable_id',
        'attachmentable_type',
        'is_active',
        'created_by',
        'updated_by',
        'uuid',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function scopeGallery($query)
    {
        return $query->where('role', self::ROLE_GALLERY);
    }

    public function scopeCovers($query)
    {
        return $query->where('role', self::ROLE_COVER);
    }

    public function scopeVideos($query)
    {
        return $query->where('role', self::ROLE_VIDEO);
    }

    public function scopeOfRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function getUrlAttribute(): string
    {
        if ($this->name) {
            return asset('storage/uploads/' . $this->name);
        }
        return $this->path ? asset(ltrim($this->path, '/')) : '';
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->role === self::ROLE_VIDEO;
    }

    public function attachmentType()
    {
        return $this->belongsTo(AttachmentType::class, 'attachment_type_id');
    }

    public function attachmentable()
    {
        return $this->morphTo();
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
