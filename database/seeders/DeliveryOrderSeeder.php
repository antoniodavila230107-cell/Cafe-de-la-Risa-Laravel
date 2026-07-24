<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeliveryOrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@cafedelarisa.com')->first();
        $product = Product::first();

        if (!$product) {
            return;
        }

        // Crear pedido de demostración de Delivery (evitando duplicados)
        $order = Order::updateOrCreate(
            ['folio' => 'VDELIV-DEMO-01'],
            [
                'user_id'               => $user?->id,
                'customer_name'         => 'Cliente Ejemplo (Delivery)',
                'customer_phone'        => '5511223344',
                'customer_email'        => 'cliente.delivery@example.com',
                'service_type'          => 'delivery',
                'delivery_street'       => 'Av. Reforma',
                'delivery_number'       => '222 Int 3A',
                'delivery_neighborhood' => 'Col. Juárez',
                'delivery_references'   => 'Portón blanco, junto a la farmacia',
                'subtotal'              => $product->price * 2,
                'discount'              => 0,
                'total'                 => $product->price * 2,
                'payment_method'        => 'online',
                'payment_status'        => 'paid',
                'order_status'          => 'preparing',
                'qr_token'              => Str::uuid()->toString(),
                'qr_used'               => false,
                'card_last_four'        => '4532',
            ]
        );

        if ($order->wasRecentlyCreated) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'item_code'  => $product->code,
                'item_name'  => $product->name,
                'unit_price' => $product->price,
                'quantity'   => 2,
                'subtotal'   => $product->price * 2,
            ]);
        }
    }
}
