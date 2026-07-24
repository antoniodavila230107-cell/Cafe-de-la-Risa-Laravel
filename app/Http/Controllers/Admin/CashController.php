<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashShift;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function index()
    {
        $activeShift = CashShift::with('movements.order')->where('status', 'open')->latest()->first();
        $pastShifts = CashShift::where('status', 'closed')->latest()->take(10)->get();

        return view('admin.cash.index', compact('activeShift', 'pastShifts'));
    }

    public function openShift(Request $request)
    {
        $validated = $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        if (CashShift::where('status', 'open')->exists()) {
            return back()->with('error', 'Ya existe un turno de caja abierto.');
        }

        CashShift::create([
            'user_id' => auth()->id(),
            'opening_amount' => $validated['opening_amount'],
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return redirect()->route('admin.cash.index')->with('success', 'Turno de caja abierto exitosamente.');
    }

    public function closeShift(Request $request, CashShift $shift)
    {
        $validated = $request->validate([
            'closing_amount' => 'required|numeric|min:0',
        ]);

        $shift->update([
            'closing_amount' => $validated['closing_amount'],
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        return redirect()->route('admin.cash.index')->with('success', 'Turno de caja cerrado correctamente.');
    }

    public function movement(Request $request)
    {
        $validated = $request->validate([
            'cash_shift_id' => 'required|exists:cash_shifts,id',
            'type' => 'required|in:entrada,salida',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        CashMovement::create([
            'cash_shift_id' => $validated['cash_shift_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'Movimiento de caja registrado exitosamente.');
    }
}
