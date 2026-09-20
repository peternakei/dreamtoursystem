<?php

namespace App\Project\Modules\Core\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleHasPermission extends Model
{
    use HasFactory;

    protected $table = "role_has_permissions";

    protected $fillable = ['role_id', 'permission_id'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id', 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'id', 'permission_id');
    }
}
