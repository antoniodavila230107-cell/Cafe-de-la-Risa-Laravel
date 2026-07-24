<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Table;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $categories = Category::where('active', true)->orderBy('order')->get();
        $products = Product::with('category')->where('active', true)->get();
        $zones = Zone::with(['tables' => function ($q) {
            $q->where('status', 'available');
        }])->where('active', true)->get();

        return view('store.comprar', compact('categories', 'products', 'zones'));
    }

    public function checkout(Request $request)
    {
        // Requiere autenticación para ordenar
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Debes iniciar sesión con Google para realizar un pedido.',
                'redirect_login' => route('auth.google'),
            ], 401);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'customer_name'            => 'required|string|max:120',
            'customer_phone'           => 'nullable|string|max:30',
            'payment_method'           => 'required|in:online,efectivo',
            'table_id'                 => 'nullable|exists:tables,id',
            'coupon_code'              => 'nullable|string',
            'preferred_time'           => 'nullable|string|max:50',
            'notes'                    => 'nullable|string|max:255',
            'card_number'              => 'required_if:payment_method,online|nullable|string',
            'cart_items'               => 'required|array|min:1',
            'cart_items.*.product_id'  => 'required|exists:products,id',
            'cart_items.*.quantity'    => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validated['cart_items'] as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);

            if (!$product->active) {
                return response()->json(['error' => "El producto {$product->name} ya no está disponible."], 422);
            }

            if ($product->stock < $itemData['quantity']) {
                return response()->json(['error' => "Inventario insuficiente para {$product->name}."], 422);
            }

            $itemSubtotal = round($product->price * $itemData['quantity'], 2);
            $subtotal += $itemSubtotal;

            $orderItemsData[] = [
                'product'    => $product,
                'quantity'   => $itemData['quantity'],
                'unit_price' => $product->price,
                'subtotal'   => $itemSubtotal,
            ];
        }

        // Validar cupón
        $discount = 0;
        $coupon = null;
        if (!empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper(trim($validated['coupon_code'])))->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $total    = max(0, round($subtotal - $discount, 2));
        $folio    = 'V' . date('ymd') . '-' . rand(1000, 9999);
        $qrToken  = Str::uuid()->toString();

        $order = DB::transaction(function () use ($validated, $user, $subtotal, $discount, $total, $folio, $qrToken, $orderItemsData, $coupon) {
            $order = Order::create([
                'user_id'        => $user->id,
                'folio'          => $folio,
                'customer_name'  => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $user->email,          // siempre desde la cuenta
                'service_type'   => 'para_llevar',
                'table_id'       => $validated['table_id'] ?? null,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'online' ? 'paid' : 'pending',
                'order_status'   => 'received',
                'qr_token'       => $qrToken,
                'qr_used'        => false,
                'preferred_time' => $validated['preferred_time'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'card_last_four' => !empty($validated['card_number']) ? substr($validated['card_number'], -4) : null,
            ]);

            foreach ($orderItemsData as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product']->id,
                    'item_code'  => $item['product']->code,
                    'item_name'  => $item['product']->name,
                    'unit_price' => $item['unit_price'],
                    'quantity'   => $item['quantity'],
                    'subtotal'   => $item['subtotal'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);

                $recipe = Recipe::with('items.ingredient.inventory')->where('product_id', $item['product']->id)->first();
                if ($recipe) {
                    foreach ($recipe->items as $recipeItem) {
                        $consumedQty = $recipeItem->quantity * $item['quantity'];
                        if ($recipeItem->ingredient && $recipeItem->ingredient->inventory) {
                            $recipeItem->ingredient->inventory->decrement('current_quantity', $consumedQty);
                        }
                    }
                }
            }

            if ($order->payment_status === 'paid') {
                Payment::create([
                    'order_id'       => $order->id,
                    'payment_method' => 'online',
                    'amount'         => $total,
                    'reference'      => 'TARJETA-SIMULADA-' . $order->card_last_four,
                    'status'         => 'approved',
                ]);
            }

            if (!empty($validated['table_id'])) {
                $table = Table::find($validated['table_id']);
                if ($table && $table->status === 'available') {
                    $table->update([
                        'status'               => 'reserved',
                        'reserved_until'       => now()->addMinutes(15),
                        'current_order_folio'  => $order->folio,
                    ]);
                }
            }

            if ($coupon) {
                $coupon->increment('uses_count');
            }

            return $order;
        });

        return response()->json([
            'success'  => true,
            'redirect' => route('store.confirmacion', ['folio' => $order->folio]),
        ]);
    }

    public function confirmacion($folio)
    {
        $order = Order::with(['items', 'table.zone'])->where('folio', $folio)->firstOrFail();
        return view('store.confirmacion', compact('order'));
    }
}
