<?php

namespace App\Project\Modules\Core\Menus\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Project\Modules\Core\Menus\Menu;

class SaveNewMenuFormAction
{
    public function handle(Request $request)
    {
        DB::beginTransaction();

        $data = [
            'name' => htmlspecialchars($request->name),
            'title' => htmlspecialchars($request->title),
            'url' => $request->url,
            'icon' => htmlspecialchars($request->icon),
            'ordering' => filter_var($request->ordering, FILTER_VALIDATE_INT),
            'created_by' => Auth::user()->id,
        ];

        if ($request->parent != 'Select parent menu') {
            $data['menu_id'] = $request->parent;
        }

        $save = Menu::create($data);
        if (!$save) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to save menu details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Menu created successfully'];
    }
}
