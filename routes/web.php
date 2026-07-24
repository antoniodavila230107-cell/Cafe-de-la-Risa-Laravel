<?php

use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// Tienda Cliente
Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::get('/comprar', [StoreController::class, 'index'])->name('store.comprar');
Route::post('/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::get('/confirmacion/{folio}', [StoreController::class, 'confirmacion'])->name('store.confirmacion');
Route::get('/acerca', function () {
    return view('store.acerca');
})->name('store.acerca');

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Recepción y Monitor de Cocina (Requiere login con rol admin o reception)
Route::middleware(['auth', 'role:admin,reception'])->group(function () {
    Route::get('/recepcion', [ReceptionController::class, 'index'])->name('reception.index');
    Route::post('/recepcion/validar', [ReceptionController::class, 'validateQr'])->name('reception.validateQr');
    Route::post('/recepcion/entregar/{order}', [ReceptionController::class, 'deliver'])->name('reception.deliver');

    Route::get('/cocina', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/cocina/update/{order}', [KitchenController::class, 'updateStatus'])->name('kitchen.updateStatus');
});

// Panel Administrativo (Requiere login con rol admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Productos
    Route::get('/productos', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/productos', [ProductController::class, 'store'])->name('admin.products.store');
    Route::post('/productos/{product}/update', [ProductController::class, 'update'])->name('admin.products.update');
    Route::post('/productos/{product}/toggle', [ProductController::class, 'toggle'])->name('admin.products.toggle');

    // Categorías
    Route::get('/categorias', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::post('/categorias/{category}/update', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::post('/categorias/{category}/toggle', [CategoryController::class, 'toggle'])->name('admin.categories.toggle');

    // Inventario e Insumos
    Route::get('/inventario', [InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::post('/inventario/insumos', [InventoryController::class, 'storeIngredient'])->name('admin.inventory.storeIngredient');
    Route::post('/inventario/ajustar', [InventoryController::class, 'adjust'])->name('admin.inventory.adjust');

    // Recetas
    Route::get('/recetas', [RecipeController::class, 'index'])->name('admin.recipes.index');
    Route::post('/recetas', [RecipeController::class, 'store'])->name('admin.recipes.store');
    Route::delete('/recetas/item/{item}', [RecipeController::class, 'removeItem'])->name('admin.recipes.removeItem');

    // Mesas & Zonas
    Route::get('/mesas', [TableController::class, 'index'])->name('admin.tables.index');
    Route::post('/mesas/zonas', [TableController::class, 'storeZone'])->name('admin.tables.storeZone');
    Route::post('/mesas', [TableController::class, 'storeTable'])->name('admin.tables.storeTable');
    Route::post('/mesas/{table}/status', [TableController::class, 'updateStatus'])->name('admin.tables.updateStatus');

    // Caja & Turnos
    Route::get('/caja', [CashController::class, 'index'])->name('admin.cash.index');
    Route::post('/caja/abrir', [CashController::class, 'openShift'])->name('admin.cash.openShift');
    Route::post('/caja/{shift}/cerrar', [CashController::class, 'closeShift'])->name('admin.cash.closeShift');
    Route::post('/caja/movimiento', [CashController::class, 'movement'])->name('admin.cash.movement');

    // Gastos Operativos
    Route::get('/gastos', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::post('/gastos', [ExpenseController::class, 'store'])->name('admin.expenses.store');

    // Reportes & Ganancias
    Route::get('/reportes', [ReportController::class, 'index'])->name('admin.reports.index');
});
