<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::with('inventory')->orderBy('name')->get();
        $recentMovements = InventoryMovement::with(['ingredient', 'user'])->latest()->take(15)->get();

        return view('admin.inventory.index', compact('ingredients', 'recentMovements'));
    }

    public function storeIngredient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:140',
            'unit' => 'required|string|max:20',
            'unit_cost' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'initial_stock' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $ingredient = Ingredient::create([
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'unit_cost' => $validated['unit_cost'],
                'min_stock' => $validated['min_stock'],
                'active' => true,
            ]);

            Inventory::create([
                'ingredient_id' => $ingredient->id,
                'current_quantity' => $validated['initial_stock'],
            ]);

            if ($validated['initial_stock'] > 0) {
                InventoryMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => auth()->id(),
                    'type' => 'entrada',
                    'quantity' => $validated['initial_stock'],
                    'unit_cost' => $validated['unit_cost'],
                    'notes' => 'Carga inicial de inventario',
                ]);
            }
        });

        return redirect()->route('admin.inventory.index')->with('success', 'Insumo e inventario creados exitosamente en MySQL.');
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'type' => 'required|in:entrada,salida,ajuste,merma',
            'quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:255',
        ]);

        $ingredient = Ingredient::findOrFail($validated['ingredient_id']);
        $inventory = Inventory::firstOrCreate(['ingredient_id' => $ingredient->id]);

        DB::transaction(function () use ($validated, $ingredient, $inventory) {
            $type = $validated['type'];
            $qty = $validated['quantity'];

            if (in_array($type, ['entrada'])) {
                $inventory->increment('current_quantity', $qty);
            } elseif (in_array($type, ['salida', 'merma'])) {
                $inventory->decrement('current_quantity', $qty);
            } elseif ($type === 'ajuste') {
                $inventory->update(['current_quantity' => $qty]);
            }

            InventoryMovement::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => $qty,
                'unit_cost' => $ingredient->unit_cost,
                'notes' => $validated['notes'] ?? ('Ajuste manual de tipo ' . $type),
            ]);
        });

        return redirect()->route('admin.inventory.index')->with('success', "Inventario de {$ingredient->name} actualizado.");
    }
}
