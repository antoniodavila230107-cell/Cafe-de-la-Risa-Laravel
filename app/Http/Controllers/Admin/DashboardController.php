<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySalesSum = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');

        $todayOrdersCount = Order::whereDate('created_at', today())->count();
        $pendingOrdersCount = Order::where('order_status', '!=', 'delivered')
            ->where('order_status', '!=', 'cancelled')
            ->count();

        $availableTablesCount = Table::where('status', 'available')->count();
        $totalTablesCount = Table::count();

        $recentOrders = Order::with('items')->latest()->take(7)->get();
        $productsCount = Product::where('active', true)->count();

        return view('admin.dashboard', compact(
            'todaySalesSum',
            'todayOrdersCount',
            'pendingOrdersCount',
            'availableTablesCount',
            'totalTablesCount',
            'recentOrders',
            'productsCount'
        ));
    }
}
