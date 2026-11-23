<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Gestión
Route::resource('customers', CustomerController::class)->except('show');
Route::resource('products', ProductController::class)->except('show');
Route::resource('inventories', InventoryController::class)->except('show');
// Una venta registrada no se edita: se consulta (show) y, si hubo un error, se anula (destroy)
Route::resource('sales', SaleController::class)->except(['edit', 'update']);

// Pedidos personalizados: show sirve para el seguimiento; la etapa se cambia con actualizarEstado()
Route::resource('orders', OrderController::class);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
// Un pago es un recibo: se registra y, si hubo un error, se anula
Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
// La entrega se programa y luego se confirma con confirmarEntrega()
Route::resource('deliveries', DeliveryController::class)->except('show');
Route::patch('deliveries/{delivery}/confirm', [DeliveryController::class, 'confirm'])->name('deliveries.confirm');

// Configuraciones
Route::resource('roles', RoleController::class)->except('show');
Route::resource('users', UserController::class)->except('show');
Route::resource('categories', CategoryController::class)->except('show');

Route::view('/login', 'auth.login')->name('login');
