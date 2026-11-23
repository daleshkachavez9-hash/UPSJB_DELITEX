<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
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
}
