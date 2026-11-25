<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Production;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    // Resumen del día: lo que hay que atender en pedidos, taller, almacén y cobros
    public function index(): View
    {
        $activos = Order::whereIn('status', ['pendiente', 'en_produccion', 'terminado']);

        $resumen = [
            'pedidos' => (clone $activos)->count(),
            'listos' => Order::where('status', 'terminado')->count(),
            'produccion' => Production::where('status', 'en_proceso')->count(),
            'porIniciar' => Production::where('status', 'pendiente')->count(),
            'stockBajo' => Inventory::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            // Saldo que todavía deben los clientes en pedidos no entregados
            'porCobrar' => (clone $activos)->withSum('payments', 'amount')->get()
                ->sum(fn ($o) => max(0, (float) $o->total - (float) $o->payments_sum_amount)),
        ];

        return view('principal.dashboard.index', compact('resumen'));
    }
}
