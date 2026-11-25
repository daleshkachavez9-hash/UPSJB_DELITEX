<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[Fillable(['user_id', 'type', 'generated_date', 'content'])]
class Report extends Model
{
    // Tipos de reporte que puede generar el sistema
    const TIPOS = ['Ventas', 'Pedidos', 'Pagos', 'Inventario', 'Producción'];

    protected function casts(): array
    {
        return [
            'generated_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Arma el contenido del reporte con los datos del mes de generated_date
    public function generarReporte(): string
    {
        $fecha = $this->generated_date ?? now();
        $inicio = $fecha->copy()->startOfMonth();
        $fin = $fecha->copy()->endOfMonth();
        $periodo = ucfirst($fecha->translatedFormat('F Y'));
        $soles = fn ($monto) => 'S/ ' . number_format((float) $monto, 2);

        $lineas = match ($this->type) {
            'Ventas' => $this->resumenVentas($inicio, $fin, $periodo, $soles),
            'Pedidos' => $this->resumenPedidos($inicio, $fin, $periodo, $soles),
            'Pagos' => $this->resumenPagos($inicio, $fin, $periodo, $soles),
            'Inventario' => $this->resumenInventario($fecha),
            'Producción' => $this->resumenProduccion($inicio, $fin, $periodo),
            default => [],
        };

        return implode("\n", $lineas);
    }

    private function resumenVentas($inicio, $fin, $periodo, $soles): array
    {
        $ventas = Sale::whereBetween('date', [$inicio, $fin->copy()->endOfDay()]);
        $lineas = [
            "Reporte de ventas - $periodo",
            'Ventas registradas: ' . (clone $ventas)->count(),
            'Total vendido: ' . $soles((clone $ventas)->sum('total')),
            'IGV incluido: ' . $soles((clone $ventas)->sum('igv')),
        ];

        $masVendidos = DetailSale::join('sales', 'sales.id', '=', 'detail_sales.sale_id')
            ->join('products', 'products.id', '=', 'detail_sales.product_id')
            ->whereBetween('sales.date', [$inicio, $fin->copy()->endOfDay()])
            ->groupBy('products.name')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get(['products.name', DB::raw('SUM(detail_sales.quantity) as cantidad')]);

        if ($masVendidos->isNotEmpty()) {
            $lineas[] = '';
            $lineas[] = 'Productos más vendidos:';
            foreach ($masVendidos as $p) {
                $lineas[] = "- {$p->name}: {$p->cantidad}";
            }
        }

        return $lineas;
    }

    private function resumenPedidos($inicio, $fin, $periodo, $soles): array
    {
        $pedidos = Order::whereBetween('order_date', [$inicio, $fin]);
        $lineas = [
            "Reporte de pedidos - $periodo",
            'Pedidos registrados: ' . (clone $pedidos)->count(),
            'Monto total de los pedidos: ' . $soles((clone $pedidos)->sum('total')),
            '',
            'Pedidos por estado:',
        ];

        $porEstado = (clone $pedidos)->selectRaw('status, COUNT(*) as cantidad')->groupBy('status')->pluck('cantidad', 'status');
        foreach (Order::ESTADOS as $valor => $texto) {
            $lineas[] = "- $texto: " . ($porEstado[$valor] ?? 0);
        }

        return $lineas;
    }

    private function resumenPagos($inicio, $fin, $periodo, $soles): array
    {
        $pagos = Payment::whereBetween('payment_date', [$inicio, $fin]);
        $lineas = [
            "Reporte de pagos - $periodo",
            'Pagos registrados: ' . (clone $pagos)->count(),
            'Total cobrado: ' . $soles((clone $pagos)->sum('amount')),
            '',
            'Cobrado por forma de pago:',
        ];

        $porTipo = (clone $pagos)->selectRaw('payment_type, SUM(amount) as monto')->groupBy('payment_type')->pluck('monto', 'payment_type');
        foreach ($porTipo as $tipo => $monto) {
            $lineas[] = "- $tipo: " . $soles($monto);
        }
        if ($porTipo->isEmpty()) {
            $lineas[] = '- Sin pagos en el periodo';
        }

        // Saldo que todavía deben los clientes en pedidos no entregados
        $pendiente = Order::where('status', '!=', 'entregado')->withSum('payments', 'amount')->get()
            ->sum(fn ($o) => max(0, (float) $o->total - (float) $o->payments_sum_amount));
        $lineas[] = '';
        $lineas[] = 'Saldo pendiente de cobro (pedidos no entregados): ' . $soles($pendiente);

        return $lineas;
    }

    private function resumenProduccion($inicio, $fin, $periodo): array
    {
        $terminadas = Production::where('status', 'terminado')->whereBetween('end_date', [$inicio, $fin]);
        $lineas = [
            "Reporte de producción - $periodo",
            'Trabajos terminados en el mes: ' . (clone $terminadas)->count(),
            'Trabajos en proceso: ' . Production::where('status', 'en_proceso')->count(),
            'Trabajos por iniciar: ' . Production::where('status', 'pendiente')->count(),
            '',
            'En proceso por etapa:',
        ];

        $porEtapa = Production::where('status', 'en_proceso')->selectRaw('stage, COUNT(*) as cantidad')->groupBy('stage')->pluck('cantidad', 'stage');
        foreach (Production::ETAPAS as $valor => $texto) {
            $lineas[] = "- $texto: " . ($porEtapa[$valor] ?? 0);
        }

        $porResponsable = (clone $terminadas)->join('users', 'users.id', '=', 'productions.user_id')
            ->groupBy('users.name')->orderByDesc('cantidad')
            ->get(['users.name', DB::raw('COUNT(*) as cantidad')]);
        if ($porResponsable->isNotEmpty()) {
            $lineas[] = '';
            $lineas[] = 'Terminados por responsable:';
            foreach ($porResponsable as $r) {
                $lineas[] = "- {$r->name}: {$r->cantidad}";
            }
        }

        return $lineas;
    }

    private function resumenInventario($fecha): array
    {
        $registros = Inventory::with('product')->get();
        $alertas = $registros->filter(fn ($i) => $i->generarAlerta());

        $lineas = [
            'Reporte de inventario al ' . $fecha->format('d/m/Y'),
            'Productos en inventario: ' . $registros->count(),
            'Productos con stock bajo: ' . $alertas->count(),
        ];

        if ($alertas->isNotEmpty()) {
            $lineas[] = '';
            $lineas[] = 'Por reponer (stock actual / stock mínimo):';
            foreach ($alertas as $i) {
                $lineas[] = "- {$i->product->name}: " . (float) $i->current_stock . ' / ' . (float) $i->minimum_stock . " {$i->product->unit}";
            }
        }

        return $lineas;
    }
}
