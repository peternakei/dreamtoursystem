<?php

namespace App\Project\Modules\Core\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Permissions\ModelHasPermission;
use App\Project\Modules\Core\Roles\ModelHasRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SystemUser extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'uuid',
    ];

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

    public function roles()
    {
        return $this->hasManyThrough(ModelHasRole::class, User::class, 'profile_id', 'model_id',);
    }

    public function permissions()
    {
        return $this->hasManyThrough(ModelHasPermission::class,  User::class, 'profile_id', 'model_id');
    }

    public function login()
    {
        return $this->hasOne(User::class, 'profile_id')->where('profile', 'SystemUser');
    }

    public function scopeVisibleTo($query, ?User $viewer = null)
    {
        $viewer ??= Auth::user();

        if ($viewer && ! $viewer->hasRole('SuperAdmin')) {
            $query->whereDoesntHave('login.roles', function ($roleQuery) {
                $roleQuery->where('name', 'SuperAdmin');
            });
        }

        return $query;
    }

    public function scopeUsers($query)
    {
        return $query->visibleTo()->orderBy('system_users.created_at', 'DESC')->get();
    }

    public function scopeActive($query)
    {
        return $query->visibleTo()->where(['system_users.is_active' => true])->orderBy('system_users.created_at', 'DESC')->get();
    }

    public function scopeInactive($query)
    {
        return $query->visibleTo()->where(['system_users.is_active' => false])->orderBy('system_users.created_at', 'DESC')->get();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::orderedUuid();
            $model->is_active = true;
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
