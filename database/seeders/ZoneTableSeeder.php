<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneTableSeeder extends Seeder
{
    public function run(): void
    {
        $zonesData = [
            'Interior climatizado' => [
                'description' => 'Área interna confortable con aire acondicionado',
                'tables' => [
                    ['number' => 'M1', 'capacity' => 4],
                    ['number' => 'M2', 'capacity' => 2],
                    ['number' => 'M3', 'capacity' => 6],
                    ['number' => 'P1', 'capacity' => 4],
                    ['number' => 'P2', 'capacity' => 2],
                ],
            ],
            'Barra rápida' => [
                'description' => 'Asientos en barra para servicio exprés',
                'tables' => [
                    ['number' => 'B1', 'capacity' => 1],
                    ['number' => 'B2', 'capacity' => 1],
                ],
            ],
            'Terraza' => [
                'description' => 'Mesas al aire libre',
                'tables' => [
                    ['number' => 'T1', 'capacity' => 4],
                    ['number' => 'T2', 'capacity' => 4],
                ],
            ],
            'Zona de fumadores' => [
                'description' => 'Área exterior delimitada',
                'tables' => [
                    ['number' => 'F1', 'capacity' => 2],
                    ['number' => 'F2', 'capacity' => 4],
                ],
            ],
            'Pickup' => [
                'description' => 'Zona de espera para entrega inmediata',
                'tables' => [],
            ],
        ];

        foreach ($zonesData as $zoneName => $data) {
            $zone = Zone::firstOrCreate(
                ['name' => $zoneName],
                ['description' => $data['description'], 'active' => true]
            );

            foreach ($data['tables'] as $tableData) {
                Table::firstOrCreate(
                    ['zone_id' => $zone->id, 'number' => $tableData['number']],
                    ['capacity' => $tableData['capacity'], 'status' => 'available']
                );
            }
        }
    }
}
