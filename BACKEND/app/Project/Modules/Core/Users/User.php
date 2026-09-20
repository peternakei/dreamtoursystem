<?php

namespace App\Project\Modules\Core\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use App\Project\Modules\Core\Users\SystemUser;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'must_change_password',
        'profile',
        'profile_id',
        'created_by',
        'updated_by',
        'status',
        'is_active',
        'uuid',
        'remember_token',
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function scopeUsers()
    {
        return $this->orderBy('users.created_at', 'DESC')->get();
    }

    public function scopeActive()
    {
        $property_id = Auth::user()->userProperty->first()->property->id;
        if ($property_id != 1) {
            return $this->join('property_user', 'property_user.user_id', '=', 'users.id')
                ->select('users.*')
                ->where(['property_user.property_id' => $property_id, 'users.is_active' => true])
                ->orderBy('users.created_at', 'DESC')->get();
        } else {
            return $this->where(['users.is_active' => true])->orderBy('users.created_at', 'DESC')->get();
        }
    }

    public function scopeInactive()
    {
        return $this->where(['users.is_active' => false])->orderBy('users.created_at', 'DESC')->get();
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = (new Carbon($value))->toDayDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->attributes['updated_at'] = (new Carbon($value))->toDayDateTimeString();
    }

    public function getDeletedAtAttribute($value)
    {
        return $this->attributes['deleted_at'] = (new Carbon($value))->toDayDateTimeString();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function userProfile()
    {
        $profile = User::find(Auth::user()->id)->profile;

        switch ($profile) {
            case 'SystemUser':
                $user = $this->hasOne(SystemUser::class, 'id', 'profile_id');
                break;
            case 'Tourist':
                $user = $this->hasOne(Tourist::class, 'id', 'profile_id');
                break;
            default:
                $user = $this->hasOne(SystemUser::class, 'id', 'profile_id');
                break;
        }

        return $user;
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
