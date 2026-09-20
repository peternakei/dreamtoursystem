<?php

namespace App\Project\Modules\Core\Menus\Seeders;

use App\Project\Modules\Core\Menus\Menu;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Idempotent — safe to re-run.
 *
 * Adds two new top-level menus (Content Library, Configuration) right after
 * Trips, and re-parents `categories`, `activities`, `addons` from Settings
 * into Configuration.
 */
class LibraryMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure top-level Content Library exists
        $contentLibrary = $this->upsertTopLevel(
            name: 'content_library',
            title: 'Content Library',
            icon: 'uil-books',
            ordering: 4,
        );

        $this->upsertChild(
            parent: $contentLibrary,
            name: 'library_hub',
            title: 'Library Hub',
            url: 'library.index',
            icon: 'uil-apps',
            ordering: 1,
        );

        // 2. Ensure top-level Configuration exists
        $configuration = $this->upsertTopLevel(
            name: 'configuration',
            title: 'Configuration',
            icon: 'uil-wrench',
            ordering: 5,
        );

        // 3. Add new children that didn't exist before
        $this->upsertChild(
            parent: $configuration,
            name: 'accommodations',
            title: 'Accommodations',
            url: 'accommodations.index',
            icon: '',
            ordering: 1,
        );

        $this->upsertChild(
            parent: $configuration,
            name: 'vehicles',
            title: 'Vehicles',
            url: 'vehicles.index',
            icon: '',
            ordering: 2,
        );

        // 4. Re-parent existing children from Settings to Configuration
        //    (categories, activities, addons). They keep their existing ids
        //    so any permission_menus rows linking to them remain valid.
        $reparented = [
            'categories' => 3,
            'activities' => 4,
            'addons' => 5,
        ];
        foreach ($reparented as $name => $ordering) {
            Menu::where('name', $name)->update([
                'menu_id' => $configuration->id,
                'ordering' => $ordering,
                'updated_at' => Carbon::now(),
            ]);
        }

        // 5. Bump existing top-level menus that should sit AFTER Configuration
        //    (idempotent: explicit set, no relative arithmetic).
        $topLevelOrdering = [
            'requests' => 6,
            'payments' => 7,
            'cms' => 8,
            'user_experiences' => 9,
        ];
        foreach ($topLevelOrdering as $name => $ordering) {
            Menu::whereNull('menu_id')->where('name', $name)->update([
                'ordering' => $ordering,
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    protected function upsertTopLevel(string $name, string $title, string $icon, int $ordering): Menu
    {
        $menu = Menu::firstOrNew(['name' => $name]);
        $menu->title = $title;
        $menu->icon = $icon;
        $menu->url = '#';
        $menu->menu_id = null;
        $menu->ordering = $ordering;
        $menu->is_active = true;
        $menu->created_by = $menu->created_by ?: 1;
        if (!$menu->exists) {
            $menu->uuid = (string) Str::orderedUuid();
            $menu->created_at = Carbon::now();
        }
        $menu->updated_at = Carbon::now();
        $menu->save();

        return $menu;
    }

    protected function upsertChild(Menu $parent, string $name, string $title, string $url, string $icon, int $ordering): Menu
    {
        $menu = Menu::firstOrNew(['name' => $name]);
        $menu->title = $title;
        $menu->icon = $icon;
        $menu->url = $url;
        $menu->menu_id = $parent->id;
        $menu->ordering = $ordering;
        $menu->is_active = true;
        $menu->created_by = $menu->created_by ?: 1;
        if (!$menu->exists) {
            $menu->uuid = (string) Str::orderedUuid();
            $menu->created_at = Carbon::now();
        }
        $menu->updated_at = Carbon::now();
        $menu->save();

        return $menu;
    }
}
