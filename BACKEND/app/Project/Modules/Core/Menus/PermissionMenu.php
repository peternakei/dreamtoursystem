<?php

namespace App\Project\Modules\Core\Menus;

use App\Project\Modules\Core\Menus\Menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Project\Modules\Core\Users\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionMenu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'permission_id',
        'menu_id',
        'created_by',
        'updated_by',
        'is_active',
        'uuid',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function scopeActive()
    {
        return $this->where('is_active', true);
    }

    public function scopeInactive()
    {
        return $this->where('is_active', false);
    }

    public function getIsActiveAttribute($value)
    {
        return $this->attributes['is_active'] = $value ? 'Active' : 'Inactive';
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
