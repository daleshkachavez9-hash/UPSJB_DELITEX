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

    // Método del diagrama: el pedido solo se entrega cuando está pagado por completo.
    public function confirmarEntrega(): bool
    {
        $pagado = (float) $this->order->payments()->sum('amount');

        if ($pagado < (float) $this->order->total) {
            return false;
        }

        $this->update(['status' => 'entregado', 'delivery_date' => now()]);
        $this->order->actualizarEstado('entregado');

        return true;
    }
}
