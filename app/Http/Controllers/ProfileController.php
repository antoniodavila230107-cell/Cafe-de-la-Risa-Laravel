<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = $user->orders()
            ->with(['items'])
            ->latest()
            ->get();

        $stats = [
            'total_orders'   => $orders->count(),
            'total_spent'    => $orders->where('payment_status', 'paid')->sum('total'),
            'orders_pending' => $orders->whereIn('order_status', ['pending', 'confirmed', 'preparing'])->count(),
            'orders_done'    => $orders->where('order_status', 'delivered')->count(),
        ];

        return view('store.profile', compact('user', 'orders', 'stats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        Auth::user()->update(['name' => $request->name]);

        return redirect()->route('profile.index')->with('success', 'Perfil actualizado correctamente.');
    }
}
