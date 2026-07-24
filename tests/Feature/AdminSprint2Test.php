<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Role;
use App\Models\Table;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSprint2Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin Test',
            'email' => 'admin_test@cafe.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_admin_can_create_product_and_category(): void
    {
        $categoryResponse = $this->actingAs($this->admin)->post('/admin/categorias', [
            'name' => 'Especiales',
            'description' => 'Bebidas de temporada',
        ]);
        $categoryResponse->assertRedirect('/admin/categorias');
        $this->assertDatabaseHas('categories', ['name' => 'Especiales']);

        $category = Category::where('name', 'Especiales')->first();

        $productResponse = $this->actingAs($this->admin)->post('/admin/productos', [
            'category_id' => $category->id,
            'code' => 'P099',
            'name' => 'Frappé de Menta',
            'price' => 70.00,
            'stock' => 15,
        ]);
        $productResponse->assertRedirect('/admin/productos');
        $this->assertDatabaseHas('products', ['code' => 'P099', 'name' => 'Frappé de Menta']);
    }

    public function test_admin_can_adjust_inventory_stock(): void
    {
        $ingredient = Ingredient::create([
            'name' => 'Leche entera',
            'unit' => 'L',
            'unit_cost' => 24,
        ]);

        Inventory::create(['ingredient_id' => $ingredient->id, 'current_quantity' => 10]);

        $response = $this->actingAs($this->admin)->post('/admin/inventario/ajustar', [
            'ingredient_id' => $ingredient->id,
            'type' => 'entrada',
            'quantity' => 5,
            'notes' => 'Compra semanal',
        ]);

        $response->assertRedirect('/admin/inventario');
        $this->assertEquals(15, Inventory::where('ingredient_id', $ingredient->id)->first()->current_quantity);
    }

    public function test_release_expired_tables_command(): void
    {
        $zone = Zone::create(['name' => 'Terraza']);
        $table = Table::create([
            'zone_id' => $zone->id,
            'number' => 'T1',
            'capacity' => 4,
            'status' => 'reserved',
            'reserved_until' => now()->subMinutes(20),
            'current_order_folio' => 'V1234',
        ]);

        $this->artisan('cafe:release-expired-tables')->assertExitCode(0);

        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'status' => 'available',
            'reserved_until' => null,
        ]);
    }
}
