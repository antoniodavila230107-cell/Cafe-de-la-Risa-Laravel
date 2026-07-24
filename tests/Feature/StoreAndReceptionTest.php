<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAndReceptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_load_storefront_menu_from_mysql(): void
    {
        $category = Category::create(['name' => 'Café']);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P001',
            'name' => 'Café Americano',
            'price' => 35.50,
            'stock' => 10,
        ]);

        $response = $this->get('/comprar');

        $response->assertStatus(200);
        $response->assertSee('Café Americano');
        $response->assertSee('35.50');
    }

    public function test_checkout_creates_order_and_consumes_inventory_in_mysql(): void
    {
        $category = Category::create(['name' => 'Café']);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P001',
            'name' => 'Café Americano',
            'price' => 35.50,
            'stock' => 10,
        ]);

        $ingredient = Ingredient::create([
            'name' => 'Café espresso en grano',
            'unit' => 'Kg',
            'unit_cost' => 180,
        ]);

        $inventory = Inventory::create([
            'ingredient_id' => $ingredient->id,
            'current_quantity' => 1.000,
        ]);

        $recipe = Recipe::create([
            'product_id' => $product->id,
            'name' => 'Receta Café Americano',
        ]);

        RecipeItem::create([
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 0.018,
        ]);

        $payload = [
            'customer_name' => 'Juan Pérez',
            'customer_phone' => '5512345678',
            'payment_method' => 'online',
            'card_number' => '4532123456789010',
            'cart_items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ];

        $response = $this->postJson('/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Juan Pérez',
            'payment_status' => 'paid',
            'total' => 71.00,
        ]);

        // Verificar que el stock de producto bajó de 10 a 8
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);

        // Verificar que el inventario de insumos bajó 2 * 0.018 = 0.036 -> 0.964
        $this->assertEquals(0.964, Inventory::find($inventory->id)->current_quantity);
    }

    public function test_reception_can_deliver_order_and_mark_qr_used(): void
    {
        $receptionRole = Role::create(['name' => 'reception', 'display_name' => 'Recepción']);
        $user = User::create([
            'role_id' => $receptionRole->id,
            'name' => 'Recepción User',
            'email' => 'recepcion_test@cafe.com',
            'password' => bcrypt('password'),
        ]);

        $order = Order::create([
            'folio' => 'V123456',
            'customer_name' => 'María López',
            'service_type' => 'para_llevar',
            'subtotal' => 50,
            'total' => 50,
            'payment_method' => 'efectivo',
            'payment_status' => 'pending',
            'order_status' => 'received',
            'qr_token' => 'test-token-123',
            'qr_used' => false,
        ]);

        $response = $this->actingAs($user)->post("/recepcion/entregar/{$order->id}");

        $response->assertRedirect('/recepcion');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'qr_used' => true,
        ]);
    }
}
