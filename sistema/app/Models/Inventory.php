<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'current_stock', 'minimum_stock', 'last_update'])]
class Inventory extends Model
{
    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'last_update' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos del diagrama
    // La cantidad es positiva para un ingreso y negativa para una salida.
    public function actualizarInventario(float $cantidad): void
    {
        $this->current_stock = round((float) $this->current_stock + $cantidad, 2);
        $this->last_update = now();
        $this->save();
    }

    public function generarAlerta(): bool
    {
        return (float) $this->current_stock <= (float) $this->minimum_stock;
    }

    public function consultarStock(): float
    {
        return (float) $this->current_stock;
    }
}
