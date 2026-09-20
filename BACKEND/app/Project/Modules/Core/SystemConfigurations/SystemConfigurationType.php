<?php

namespace App\Project\Modules\Core\SystemConfigurations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemConfigurationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'updated_at', 'created_at'
    ];
}
