<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest('expense_date')->get();
        $totalExpenses = $expenses->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'category' => $validated['category'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Gasto registrado exitosamente en MySQL.');
    }
}
