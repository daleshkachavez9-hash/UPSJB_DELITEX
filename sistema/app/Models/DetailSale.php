<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sale_id', 'product_id', 'quantity', 'unit_price', 'subtotal'])]
class DetailSale extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos del diagrama
    public function calcularSubtotal(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    // Una prenda personalizada (sin producto del catálogo) no usa stock
    public function validarStock(): bool
    {
        return $this->product?->verificarStock($this->quantity) ?? true;
    }
}
