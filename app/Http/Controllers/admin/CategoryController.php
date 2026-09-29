<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:png|max:2048',
        ]);

        $path = $request->file('icon')->store('categories', 'public');

        Category::create([
            'name' => $request->name,
            'icon_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:png|max:2048',
        ]);

        $category->name = $request->name;

        if ($request->hasFile('icon')) {
            if ($category->icon_path && Storage::disk('public')->exists($category->icon_path)) {
                Storage::disk('public')->delete($category->icon_path);
            }
            $category->icon_path = $request->file('icon')->store('categories', 'public');
        }

        $category->save();

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->icon_path && Storage::disk('public')->exists($category->icon_path)) {
            Storage::disk('public')->delete($category->icon_path);
        }
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
