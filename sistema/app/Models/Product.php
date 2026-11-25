<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['category_id', 'name', 'garment_type', 'price', 'quantity', 'unit', 'status', 'image'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Solo las prendas se venden y se piden; los insumos (telas, hilos...) son del taller
    public function scopePrendas($query)
    {
        return $query->whereHas('category', fn ($q) => $q->where('type', 'prenda'));
    }

    // Producto 1 — 0..1 Inventario: cada producto tiene como máximo un registro de inventario
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function detailSales(): HasMany
    {
        return $this->hasMany(DetailSale::class);
    }

    // Método del diagrama: +verificarStock(): bool
    public function verificarStock(float $cantidad): bool
    {
        return $cantidad <= (float) $this->quantity;
    }

    // Mueve el stock del producto y de su inventario a la vez (positivo = ingreso, negativo = salida),
    // así la cantidad del producto y el stock actual del inventario siempre coinciden
    public function ajustarStock(float $cantidad): void
    {
        $this->quantity = round((float) $this->quantity + $cantidad, 2);
        $this->save();

        $this->inventory?->actualizarInventario($cantidad);
    }
}
