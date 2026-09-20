<?php

namespace App\Project\Modules\Core\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class ModelHasPermission extends Model
{
    use HasFactory;

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
