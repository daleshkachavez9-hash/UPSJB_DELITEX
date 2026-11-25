<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['customer_id', 'order_date', 'status', 'total', 'description'])]
class Order extends Model
{
    // Etapas por las que pasa un pedido personalizado
    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_produccion' => 'En producción',
        'terminado' => 'Terminado',
        'entregado' => 'Entregado',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    // Métodos del diagrama
    public function calcularTotal(): float
    {
        $this->total = round($this->details()->sum('subtotal'), 2);

        return (float) $this->total;
    }

    public function actualizarEstado(string $estado): void
    {
        $this->update(['status' => $estado]);
    }

    // Recorre los pagos en orden de fecha: los que dejan saldo son "adelanto" y el que
    // completa el total es la "cancelacion". Se vuelve a calcular al registrar o anular
    // un pago y al cambiar el total, así el estado nunca queda desactualizado.
    public function actualizarEstadoPagos(): void
    {
        $acumulado = 0;
        foreach ($this->payments()->orderBy('payment_date')->orderBy('id')->get() as $pago) {
            $acumulado = round($acumulado + (float) $pago->amount, 2);
            $estado = $acumulado >= (float) $this->total ? 'cancelacion' : 'adelanto';
            if ($pago->status !== $estado) {
                $pago->update(['status' => $estado]);
            }
        }
    }
}
