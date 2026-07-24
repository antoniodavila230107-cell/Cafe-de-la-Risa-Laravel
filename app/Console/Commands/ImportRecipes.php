<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportRecipes extends Command
{
    protected $signature = 'cafe:import-recipes {path?}';
    protected $description = 'Importa recetas e insumos de recetas.json a MySQL';

    public function handle(): int
    {
        $path = $this->argument('path') ?? storage_path('app/legacy-import/recetas.json');

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
        DB::transaction(function () use ($data, &$count) {
            foreach ($data as $recipeData) {
                $productCode = $recipeData['ProductoCodigo'] ?? null;
                if (!$productCode) {
                    continue;
                }

                $product = Product::where('code', $productCode)->first();
                if (!$product) {
                    continue;
                }

                $recipe = Recipe::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'name' => $recipeData['Nombre'] ?? ("Receta " . $product->name),
                        'yield' => $recipeData['Rendimiento'] ?? 1,
                        'instructions' => $recipeData['Preparacion'] ?? null,
                        'theoretical_cost' => $recipeData['CostoTeorico'] ?? 0,
                        'active' => true,
                    ]
                );

                RecipeItem::where('recipe_id', $recipe->id)->delete();

                if (isset($recipeData['Insumos']) && is_array($recipeData['Insumos'])) {
                    foreach ($recipeData['Insumos'] as $insumoData) {
                        $ingredient = Ingredient::firstOrCreate(
                            ['name' => $insumoData['Nombre']],
                            [
                                'unit' => $insumoData['Unidad'] ?? 'Pieza',
                                'unit_cost' => $insumoData['CostoUnitario'] ?? 0,
                                'min_stock' => 5,
                                'active' => true,
                            ]
                        );

                        Inventory::firstOrCreate(
                            ['ingredient_id' => $ingredient->id],
                            ['current_quantity' => 100]
                        );

                        RecipeItem::create([
                            'recipe_id' => $recipe->id,
                            'ingredient_id' => $ingredient->id,
                            'quantity' => $insumoData['Cantidad'] ?? 1,
                        ]);
                    }
                }
                $count++;
            }
        });

        $this->info("¡Éxito! Se importaron {$count} recetas y sus insumos a MySQL.");
        return Command::SUCCESS;
    }
}
