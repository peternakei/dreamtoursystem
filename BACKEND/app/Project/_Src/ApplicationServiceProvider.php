<?php

namespace App\Project\_Src;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach (Registry::modules() as $module) {
            $path = base_path($module['path'].'/Migrations');
            if (is_dir($path)) {
                $this->loadMigrationsFrom($path);
            }
        }
        // Keep persisted polymorphic type names compatible with existing safari data.
        Relation::morphMap(json_decode(file_get_contents(__DIR__.'/morph-map.json'), true));
    }
}
