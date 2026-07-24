<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index()
    {
        $products = Product::with(['recipe.items.ingredient'])->orderBy('name')->get();
        $ingredients = Ingredient::where('active', true)->orderBy('name')->get();

        return view('admin.recipes.index', compact('products', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.001',
            'instructions' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        DB::transaction(function () use ($validated, $product) {
            $recipe = Recipe::firstOrCreate(
                ['product_id' => $product->id],
                [
                    'name' => "Receta " . $product->name,
                    'yield' => 1,
                    'instructions' => $validated['instructions'] ?? null,
                    'active' => true,
                ]
            );

            RecipeItem::updateOrCreate(
                [
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $validated['ingredient_id'],
                ],
                [
                    'quantity' => $validated['quantity'],
                ]
            );

            // Recalcular costo teórico
            $recipe->load('items.ingredient');
            $cost = 0;
            foreach ($recipe->items as $item) {
                if ($item->ingredient) {
                    $cost += ($item->quantity * $item->ingredient->unit_cost);
                }
            }
            $recipe->update(['theoretical_cost' => $cost]);
        });

        return redirect()->route('admin.recipes.index')->with('success', "Insumo agregado a la receta de {$product->name}.");
    }

    public function removeItem(RecipeItem $item)
    {
        $recipe = $item->recipe;
        $item->delete();

        if ($recipe) {
            $recipe->load('items.ingredient');
            $cost = 0;
            foreach ($recipe->items as $i) {
                if ($i->ingredient) {
                    $cost += ($i->quantity * $i->ingredient->unit_cost);
                }
            }
            $recipe->update(['theoretical_cost' => $cost]);
        }

        return back()->with('info', 'Insumo removido de la receta.');
    }
}
