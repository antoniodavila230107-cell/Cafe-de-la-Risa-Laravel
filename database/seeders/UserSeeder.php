<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $receptionRole = Role::where('name', 'reception')->first();

        User::firstOrCreate(
            ['email' => 'admin@cafedelarisa.com'],
            [
                'role_id' => $adminRole?->id,
                'name' => 'Administrador Demo',
                'password' => 'password',
                'phone' => '5551234567',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'recepcion@cafedelarisa.com'],
            [
                'role_id' => $receptionRole?->id,
                'name' => 'Recepción Demo',
                'password' => 'password',
                'phone' => '5559876543',
                'active' => true,
            ]
        );
    }
}
