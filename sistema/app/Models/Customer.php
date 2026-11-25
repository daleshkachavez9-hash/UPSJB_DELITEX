<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'document_type', 'document_number', 'address', 'phone'])]
class Customer extends Model
{
    // Persona: DNI de 8 dígitos. Club, colegio o empresa: RUC de 11 dígitos.
    const DOCUMENTOS = ['DNI' => 8, 'RUC' => 11];

    // Documento listo para mostrar: "DNI 45678912" o "RUC 20123456789"
    public function documento(): string
    {
        return $this->document_type . ' ' . $this->document_number;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
