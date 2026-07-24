<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportProducts extends Command
{
    protected $signature = 'cafe:import-products {path?}';
    protected $description = 'Importa catálogo de productos desde productos.json a MySQL';

    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/legacy-import/productos.json');

        if (!File::exists($path)) {
            $this->warn("El archivo no existe: {$path}. Se omitió la importación.");
            return Command::SUCCESS;
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        if (!$data || !is_array($data)) {
            $this->warn("Formato JSON inválido en {$path}. Se omitió la importación.");
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($data as $item) {
            $categoryName = $item['Categoria'] ?? 'General';
            $category = Category::firstOrCreate(['name' => $categoryName]);

            Product::updateOrCreate(
                ['code' => $item['Codigo']],
                [
                    'category_id' => $category->id,
                    'name' => $item['Nombre'],
                    'price' => $item['Precio'] ?? 0,
                    'stock' => $item['Existencia'] ?? 0,
                    'description' => $item['Descripcion'] ?? null,
                    'icon' => $item['Icono'] ?? null,
                    'image' => $item['Imagen'] ?? null,
                    'active' => true,
                ]
            );
            $count++;
        }

        $this->info("¡Éxito! Se importaron/actualizaron {$count} productos desde JSON a MySQL.");
        return Command::SUCCESS;
    }
}
