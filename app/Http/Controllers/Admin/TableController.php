<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Zone;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $zones = Zone::with('tables')->where('active', true)->get();
        return view('admin.tables.index', compact('zones'));
    }

    public function updateStatus(Request $request, Table $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,reserved,occupied,maintenance',
        ]);

        $table->update([
            'status' => $validated['status'],
            'reserved_until' => $validated['status'] === 'available' ? null : $table->reserved_until,
            'current_order_folio' => $validated['status'] === 'available' ? null : $table->current_order_folio,
        ]);

        return back()->with('success', "Estado de la Mesa {$table->number} cambiado a {$table->status}.");
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:zones,name',
            'description' => 'nullable|string|max:255',
        ]);

        Zone::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'active' => true,
        ]);

        return back()->with('success', 'Zona física creada exitosamente.');
    }

    public function storeTable(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'number' => 'required|string|max:30',
            'capacity' => 'required|integer|min:1',
        ]);

        Table::create([
            'zone_id' => $validated['zone_id'],
            'number' => strtoupper($validated['number']),
            'capacity' => $validated['capacity'],
            'status' => 'available',
        ]);

        return back()->with('success', "Mesa {$validated['number']} agregada exitosamente.");
    }
}
