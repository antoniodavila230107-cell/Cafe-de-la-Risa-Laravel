<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'RISA10'],
            [
                'description' => 'Descuento del 10% en tu pedido',
                'type' => 'percent',
                'value' => 10.00,
                'max_uses' => 100,
                'active' => true,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'BIENVENIDA'],
            [
                'description' => '$20 pesos de descuento de bienvenida',
                'type' => 'amount',
                'value' => 20.00,
                'max_uses' => 50,
                'active' => true,
            ]
        );
    }
}
