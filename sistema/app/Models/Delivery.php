<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'delivery_date', 'status', 'observation'])]
class Delivery extends Model
{
    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Motivo por el que todavía no se puede entregar (null si ya se puede)
    public function impedimento(): ?string
    {
        if ($this->order->status !== 'terminado') {
            return 'El pedido todavía no termina su producción (está ' . mb_strtolower(Order::ESTADOS[$this->order->status] ?? $this->order->status) . ').';
        }

        $saldo = round((float) $this->order->total - (float) $this->order->payments()->sum('amount'), 2);
        if ($saldo > 0) {
            return 'El pedido tiene un saldo pendiente de S/ ' . number_format($saldo, 2) . '.';
        }

        return null;
    }

    // Método del diagrama: el pedido solo se entrega cuando ya está terminado y pagado por completo.
    public function confirmarEntrega(): bool
    {
        if ($this->impedimento()) {
            return false;
        }

        $this->update(['status' => 'entregado', 'delivery_date' => now()]);
        $this->order->actualizarEstado('entregado');

        return true;
    }
}
