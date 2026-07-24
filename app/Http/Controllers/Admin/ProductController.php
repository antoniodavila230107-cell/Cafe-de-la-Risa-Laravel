<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::where('active', true)->orderBy('order')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'code' => 'required|string|max:40|unique:products,code',
            'name' => 'required|string|max:140',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|string|max:255',
        ]);

        Product::create([
            'category_id' => $validated['category_id'] ?? null,
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'image' => $validated['image'] ?? '/images/cafe-risa-logo-principal.png',
            'active' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado exitosamente en MySQL.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'code' => 'required|string|max:40|unique:products,code,' . $product->id,
            'name' => 'required|string|max:140',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function toggle(Product $product)
    {
        $product->update(['active' => !$product->active]);
        return back()->with('info', "Estado del producto {$product->name} cambiado a " . ($product->active ? 'Activo' : 'Inactivo'));
    }
}
