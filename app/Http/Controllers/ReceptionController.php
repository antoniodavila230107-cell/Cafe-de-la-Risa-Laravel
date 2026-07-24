<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'table.zone'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('folio', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%");
        }

        $pendingOrders = (clone $query)->where('order_status', '!=', 'delivered')
            ->where('order_status', '!=', 'cancelled')
            ->get();

        $deliveredOrders = (clone $query)->where('order_status', 'delivered')->take(10)->get();

        $selectedOrder = null;
        if ($request->filled('token')) {
            $selectedOrder = Order::with(['items', 'table.zone'])->where('qr_token', $request->token)->first();
        }

        return view('reception.index', compact('pendingOrders', 'deliveredOrders', 'selectedOrder'));
    }

    public function validateQr(Request $request)
    {
        $request->validate(['qr_token' => 'required|string']);

        $order = Order::with(['items', 'table.zone'])->where('qr_token', trim($request->qr_token))->first();

        if (!$order) {
            return back()->with('error', 'Código QR no válido o no encontrado.');
        }

        return redirect()->route('reception.index', ['token' => $order->qr_token]);
    }

    public function deliver(Request $request, Order $order)
    {
        if ($order->order_status === 'delivered') {
            return back()->with('info', 'Este pedido ya fue entregado anteriormente.');
        }

        DB::transaction(function () use ($order) {
            // Si estaba pendiente de pago en efectivo, registrar el cobro simulado
            if ($order->payment_status === 'pending') {
                $order->payment_status = 'paid';
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'efectivo',
                    'amount' => $order->total,
                    'reference' => 'COBRO-RECEPCION-EFECTIVO',
                    'status' => 'approved',
                ]);
            }

            $order->order_status = 'delivered';
            $order->qr_used = true;
            $order->qr_used_at = now();
            $order->save();

            // Liberar mesa si estaba reservada por este pedido
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'status' => 'available',
                    'reserved_until' => null,
                    'current_order_folio' => null,
                ]);
            }
        });

        return redirect()->route('reception.index')->with('success', "¡Pedido {$order->folio} entregado exitosamente!");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:received,preparing,on_the_way,ready,delivered,cancelled',
        ]);

        $newStatus = $request->order_status;

        DB::transaction(function () use ($order, $newStatus) {
            $order->order_status = $newStatus;

            if ($newStatus === 'delivered') {
                $order->qr_used = true;
                $order->qr_used_at = now();

                if ($order->payment_status === 'pending') {
                    $order->payment_status = 'paid';
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_method' => $order->payment_method ?: 'efectivo',
                        'amount' => $order->total,
                        'reference' => 'ENTREGA-COBRO',
                        'status' => 'approved',
                    ]);
                }

                if ($order->table_id) {
                    Table::where('id', $order->table_id)->update([
                        'status' => 'available',
                        'reserved_until' => null,
                        'current_order_folio' => null,
                    ]);
                }
            }

            $order->save();
        });

        $labels = [
            'preparing'  => 'En Preparación 👨‍🍳',
            'on_the_way' => 'En Camino / Listo 🛵',
            'delivered'  => 'Entregado ✅',
        ];

        $statusText = $labels[$newStatus] ?? $newStatus;
        return back()->with('success', "Estado del pedido {$order->folio} actualizado a: {$statusText}");
    }
}
