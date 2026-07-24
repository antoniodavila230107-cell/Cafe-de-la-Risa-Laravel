<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest('expense_date')->get();
        $directExpenses = $expenses->sum('amount');

        $activeEmployees = Employee::where('active', true)->with('tables')->get();
        $payrollExpenses = $activeEmployees->sum(fn($e) => $e->monthly_salary);

        $totalExpenses = $directExpenses + $payrollExpenses;

        return view('admin.expenses.index', compact('expenses', 'directExpenses', 'payrollExpenses', 'totalExpenses', 'activeEmployees'));
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
