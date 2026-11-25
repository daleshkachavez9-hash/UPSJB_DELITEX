<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'amount', 'payment_date', 'payment_type', 'status'])]
class Payment extends Model
{
    // Qué fue el pago para su pedido (lo calcula Order::actualizarEstadoPagos)
    const ESTADOS = [
        'adelanto' => 'Adelanto',
        'cancelacion' => 'Cancelación',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Método del diagrama: saldo pendiente del pedido después de sus pagos.
    public function calcularSaldo(): float
    {
        $pagado = $this->order->payments()->sum('amount');

        return round((float) $this->order->total - $pagado, 2);
    }
}
