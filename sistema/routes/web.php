<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Inicio de sesión: solo para quien todavía no ingresó
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    // throttle: máximo 5 intentos por minuto para evitar que adivinen la contraseña
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

// Todo el sistema exige haber iniciado sesión
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    // El contenido del reporte lo arma generarReporte() del modelo
    Route::resource('reports', ReportController::class);

    // Gestión
    Route::resource('customers', CustomerController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::resource('inventories', InventoryController::class)->except('show');
    // Una venta registrada no se edita: se consulta (show) y, si hubo un error, se anula (destroy)
    Route::resource('sales', SaleController::class)->except(['edit', 'update']);

    // Pedidos personalizados: show sirve para el seguimiento; la etapa se cambia con actualizarEstado()
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    // Pedido «origina» Venta: el comprobante del pedido entregado se emite desde el pedido
    Route::post('orders/{order}/sale', [SaleController::class, 'storeFromOrder'])->name('orders.sale');
    // Un pago es un recibo: se registra y, si hubo un error, se anula
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
    // Producción: el trabajo se inicia, avanza de etapa y se termina con los métodos del diagrama
    Route::resource('productions', ProductionController::class)->except('show');
    Route::patch('productions/{production}/start', [ProductionController::class, 'start'])->name('productions.start');
    Route::patch('productions/{production}/advance', [ProductionController::class, 'advance'])->name('productions.advance');
    Route::patch('productions/{production}/finish', [ProductionController::class, 'finish'])->name('productions.finish');
    // La entrega se programa y luego se confirma con confirmarEntrega()
    Route::resource('deliveries', DeliveryController::class)->except('show');
    Route::patch('deliveries/{delivery}/confirm', [DeliveryController::class, 'confirm'])->name('deliveries.confirm');

    // Configuraciones
    Route::resource('roles', RoleController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');

    // Mi perfil y cambio de contraseña del usuario que inició sesión
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});
