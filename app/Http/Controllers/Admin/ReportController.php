<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7days');

        $startDate = match($period) {
            'today' => today(),
            '7days' => now()->subDays(7),
            '15days' => now()->subDays(15),
            'month' => now()->startOfMonth(),
            default => now()->subDays(7),
        };

        $orders = Order::with('items')
            ->where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->get();

        $totalSales = $orders->sum('total');
        $totalSubtotal = $orders->sum('subtotal');
        $totalDiscount = $orders->sum('discount');
        $ordersCount = $orders->count();
        $averageTicket = $ordersCount > 0 ? round($totalSales / $ordersCount, 2) : 0;

        $directExpenses = Expense::where('expense_date', '>=', $startDate->toDateString())->sum('amount');
        
        $activeEmployees = Employee::where('active', true)->get();
        $payrollExpenses = $activeEmployees->sum(fn($e) => $e->monthly_salary);

        $totalExpenses = $directExpenses + $payrollExpenses;
        $netIncome = max(0, round($totalSales - $totalExpenses, 2));

        return view('admin.reports.index', compact(
            'period',
            'orders',
            'totalSales',
            'totalSubtotal',
            'totalDiscount',
            'ordersCount',
            'averageTicket',
            'directExpenses',
            'payrollExpenses',
            'totalExpenses',
            'netIncome'
        ));
    }
}
