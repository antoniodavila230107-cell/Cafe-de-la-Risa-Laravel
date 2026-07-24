<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Acceso total al sistema y panel de control'],
            ['name' => 'reception', 'display_name' => 'Recepción', 'description' => 'Validación de QR, cobro y entrega de pedidos'],
            ['name' => 'customer', 'display_name' => 'Cliente', 'description' => 'Cliente web para realizar pedidos en línea'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
