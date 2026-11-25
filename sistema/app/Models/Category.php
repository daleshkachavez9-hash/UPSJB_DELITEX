<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'description'])]
class Category extends Model
{
    // Prenda: se confecciona y se vende. Insumo: se usa en el taller y no se vende.
    const TIPOS = [
        'prenda' => 'Prenda (se vende)',
        'insumo' => 'Insumo (uso del taller)',
    ];

    // Una categoría clasifica muchos productos
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
