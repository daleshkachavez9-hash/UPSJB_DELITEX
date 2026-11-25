<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'product_id', 'garment_type', 'quantity', 'size', 'color', 'fabric', 'print_type', 'print_detail', 'notes', 'unit_price', 'subtotal'])]
class OrderDetail extends Model
{
    // Tipos de prenda que confecciona el taller (los mismos del prototipo)
    const PRENDAS = ['Conjunto de fútbol', 'Camiseta deportiva', 'Short deportivo', 'Buzo deportivo', 'Casaca cortaviento', 'Polo deportivo'];

    const TELAS = ['Dry-fit', 'Microfibra', 'Piqué'];

    const ESTAMPADOS = ['Sin estampado', 'Nombre y número en la espalda', 'Logo / escudo en el pecho', 'Sublimado completo'];

    // Nombre que se muestra: el producto del catálogo o el tipo de prenda personalizada
    public function nombrePrenda(): string
    {
        return $this->product->name ?? $this->garment_type ?? 'Prenda personalizada';
    }

    // Tipo de prenda para el dibujo de la ficha (si es del catálogo se toma de su tipo de prenda)
    public function tipoPrenda(): string
    {
        return $this->garment_type ?? $this->product->garment_type ?? 'Prenda';
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
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

    public function validarDatos(): bool
    {
        return $this->quantity > 0 && (float) $this->unit_price > 0;
    }
}
