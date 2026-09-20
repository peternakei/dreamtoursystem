<?php

namespace App\Project\Modules\System\Categories;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Trips\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->orderBy('name')->get();

        return view('web.system.configuration.category.index', [
            'title' => 'Categories',
            'sub_title' => 'All Categories',
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created');
    }

    public function show(string $id)
    {
        $category = Category::where('uuid', $id)->firstOrFail();

        return view('web.system.configuration.category.show', [
            'title' => 'Category Profile',
            'sub_title' => $category->name,
            'category' => $category,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::where('uuid', $id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:150|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Category updated');
    }

    public function changeStatus(string $id)
    {
        $category = Category::where('uuid', $id)->firstOrFail();
        $category->update([
            'is_active' => !$category->is_active,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Category status changed');
    }

    public function destroy(string $id)
    {
        $category = Category::where('uuid', $id)->firstOrFail();
        $category->update(['updated_by' => Auth::id()]);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted');
    }
}
