<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Table;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $tables = Table::all();

        // 1. Mesero con Mesas Asignadas
        $waiter = Employee::create([
            'name'             => 'Carlos Mendoza (Mesero)',
            'email'            => 'carlos.mesero@cafedelarisa.com',
            'phone'            => '5511223344',
            'job_role'         => 'mesero',
            'salary'           => 6500.00,
            'salary_period'    => 'mensual',
            'register_station' => null,
            'active'           => true,
        ]);

        if ($tables->count() > 0) {
            $waiter->tables()->sync($tables->pluck('id')->take(3)->toArray());
        }

        // 2. Recepcionista con Estación de Cobro
        Employee::create([
            'name'             => 'Sofía Ramírez (Recepcionista)',
            'email'            => 'sofia.recepcion@cafedelarisa.com',
            'phone'            => '5522334455',
            'job_role'         => 'recepcionista',
            'salary'           => 7500.00,
            'salary_period'    => 'mensual',
            'register_station' => 'Caja Principal 01 - Barra',
            'active'           => true,
        ]);

        // 3. Cocinero
        Employee::create([
            'name'             => 'Mateo Hernández (Cocinero Chef)',
            'email'            => 'mateo.cocina@cafedelarisa.com',
            'phone'            => '5533445566',
            'job_role'         => 'cocinero',
            'salary'           => 9000.00,
            'salary_period'    => 'mensual',
            'register_station' => null,
            'active'           => true,
        ]);
    }
}
