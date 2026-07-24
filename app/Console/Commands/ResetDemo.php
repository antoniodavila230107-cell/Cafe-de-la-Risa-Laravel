<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDemo extends Command
{
    protected $signature = 'cafe:reset-demo';
    protected $description = 'Reinicializa la base de datos MySQL con datos demostrativos limpios e importa el catálogo legado';

    public function handle(): int
    {
        $this->info("Reinicializando entorno demostrativo de Café de la Risa...");

        Artisan::call('migrate:fresh', ['--seed' => true]);
        $this->info("✓ Base de datos refrescada y seeders ejecutados.");

        Artisan::call('cafe:import-products');
        $this->info("✓ Productos importados desde JSON legado a MySQL.");

        Artisan::call('cafe:import-recipes');
        $this->info("✓ Recetas e insumos importados a MySQL.");

        Artisan::call('cafe:import-sales');
        $this->info("✓ Histórico de ventas importado a MySQL.");

        $this->info("¡Reinicio de demostración completado con éxito! Todas las operaciones dependen 100% de MySQL.");
        return Command::SUCCESS;
    }
}
