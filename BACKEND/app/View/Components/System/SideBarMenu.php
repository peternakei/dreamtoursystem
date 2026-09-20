<?php

namespace App\View\Components\System;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Menus\Menu;
use App\Project\Modules\Core\Menus\PermissionMenu;

class SideBarMenu extends Component
{

    public $userId;
    public $userMenus;

    public $firstInitial;
    public $lastInitial;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->userId = Auth::user()->id;

        $user = Auth::user();

        // Extract user initials
        $nameParts = explode(' ', $user->userProfile->name);
        $this->firstInitial = strtoupper(substr($nameParts[0] ?? '', 0, 1));
        $this->lastInitial = strtoupper(substr($nameParts[1] ?? '', 0, 1));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        //Get user menus from permissions
        $user = User::findOrFail($this->userId);

        $permissions = $user->getAllPermissions();
        $menus = collect();

        foreach ($permissions as $permission) {
            $permMenus = PermissionMenu::where('permission_id', $permission->id)
                ->with([
                    'menu' => function ($query) {
                        $query->with([
                            'childs' => function ($query) {
                                $query->with(['childs']);
                            }
                        ])->whereNull('menu_id');
                    }
                ])->get();

            $permMenus->each(function ($permMenu) use ($menus) {
                if ($permMenu->menu && $permMenu->menu->menu_id === null) {
                    $menus->push($permMenu->menu);
                }
            });
        }

        $menus = $menus->unique('id')->sortBy('ordering')->values();
        $firstInitial = $this->firstInitial;
        $lastInitial = $this->lastInitial;
        return view('components.system.side-bar-menu', compact('menus', 'firstInitial', 'lastInitial'));
    }
}
