<?php

namespace Tests\Feature;

use App\Models\CashShift;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSprint3Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin Test 3',
            'email' => 'admin_test3@cafe.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_admin_can_open_and_close_cash_shift(): void
    {
        $openResponse = $this->actingAs($this->admin)->post('/admin/caja/abrir', [
            'opening_amount' => 500.00,
        ]);
        $openResponse->assertRedirect('/admin/caja');
        $this->assertDatabaseHas('cash_shifts', ['opening_amount' => 500.00, 'status' => 'open']);

        $shift = CashShift::where('status', 'open')->first();

        $closeResponse = $this->actingAs($this->admin)->post("/admin/caja/{$shift->id}/cerrar", [
            'closing_amount' => 1250.00,
        ]);
        $closeResponse->assertRedirect('/admin/caja');
        $this->assertDatabaseHas('cash_shifts', ['id' => $shift->id, 'status' => 'closed', 'closing_amount' => 1250.00]);
    }

    public function test_admin_can_register_expense(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/gastos', [
            'category' => 'Empaque',
            'description' => 'Vasos para café de 16oz',
            'amount' => 350.00,
            'expense_date' => date('Y-m-d'),
        ]);

        $response->assertRedirect('/admin/gastos');
        $this->assertDatabaseHas('expenses', ['description' => 'Vasos para café de 16oz', 'amount' => 350.00]);
    }

    public function test_admin_can_access_reports(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reportes?period=7days');
        $response->assertStatus(200);
        $response->assertSee('Reporte de Ingresos');
    }

    public function test_kitchen_monitor_can_update_order_status(): void
    {
        $order = Order::create([
            'folio' => 'V9999',
            'customer_name' => 'Pedro Solís',
            'service_type' => 'para_llevar',
            'subtotal' => 100,
            'total' => 100,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'order_status' => 'received',
            'qr_token' => 'kitchen-token-999',
        ]);

        $response = $this->actingAs($this->admin)->post("/cocina/update/{$order->id}", [
            'order_status' => 'preparing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'order_status' => 'preparing']);
    }
}
