<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'order' => $validated['order'] ?? 0,
            'active' => true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function toggle(Category $category)
    {
        $category->update(['active' => !$category->active]);
        return back()->with('info', "Estado de la categoría {$category->name} actualizado.");
    }
}
