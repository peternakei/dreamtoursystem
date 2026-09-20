<?php

namespace App\Project\Modules\Core\Menus;

use App\Project\Modules\Core\Menus\Services\SaveNewMenuFormAction;
use App\Project\Modules\Core\Menus\Services\SaveUpdateEditMenuDetailsFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\Core\Menus\Requests\CreateNewMenuFormRequest;
use App\Project\Modules\Core\Menus\Requests\EditMenuDetailsFormRequest;
use Illuminate\Http\Request;
use App\Project\Modules\Core\Menus\Menu;
use App\Project\Modules\Core\Menus\PermissionMenu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get menus
        $menus = Menu::orderBy('created_at', 'DESC')->get();

        return view('web.core.menu.index', [
            'menus' => $menus,
            'title' => 'Menus',
            'sub_title' => 'All Menus'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewMenuFormRequest $request, SaveNewMenuFormAction $saveNewMenuFormAction)
    {
        $save = $saveNewMenuFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'menus'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $menu = Menu::where('uuid', $id)->first();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $menu,
            'html' => '<input type="hidden" name="menu_id" id="menu_id" value="' . $menu->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditMenuDetailsFormRequest $request, SaveUpdateEditMenuDetailsFormAction $saveUpdateEditMenuDetailsFormAction, string $id)
    {
        $update = $saveUpdateEditMenuDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message']
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'menus'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::where('uuid', $id)->first();
        PermissionMenu::where('menu_id', $menu->id)->delete();

        $delete = $menu->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete menu details'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Menu deleted successfully',
            'redirect' => 'menus'
        ]);
    }
}
