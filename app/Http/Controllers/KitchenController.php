<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        $activeOrders = Order::with(['items', 'table.zone'])
            ->whereIn('order_status', ['received', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kitchen.index', compact('activeOrders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:received,preparing,ready,delivered',
        ]);

        $order->update(['order_status' => $validated['order_status']]);

        return back()->with('success', "Estado del pedido {$order->folio} cambiado a {$validated['order_status']}.");
    }
}
