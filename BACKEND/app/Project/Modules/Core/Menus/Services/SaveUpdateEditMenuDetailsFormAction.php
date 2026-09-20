<?php

namespace App\Project\Modules\Core\Menus\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Project\Modules\Core\Menus\Menu;
use Carbon\Carbon;

class SaveUpdateEditMenuDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        DB::beginTransaction();

        $menu = Menu::where('uuid', $id)->first();

        $update = $menu->update([
            'name' => htmlspecialchars($request->name),
            'title' => htmlspecialchars($request->title),
            'url' => $request->url,
            'icon' => htmlspecialchars($request->icon),
            'ordering' => filter_var($request->ordering, FILTER_VALIDATE_INT),
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now()
        ]);

        if (!$update) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Failed to update menu details'];
        }

        DB::commit();
        return ['status' => true, 'message' => 'Menu updated successfully'];
    }
}
