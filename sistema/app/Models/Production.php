<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'user_id', 'stage', 'start_date', 'end_date', 'status', 'observation'])]
class Production extends Model
{
    // Etapas del taller, en el orden en que se trabaja una prenda
    const ETAPAS = [
        'corte' => 'Corte',
        'confeccion' => 'Confección',
        'estampado' => 'Estampado o bordado',
        'acabado' => 'Acabado',
        'control_calidad' => 'Control de calidad',
    ];

    const ESTADOS = [
        'pendiente' => 'Por iniciar',
        'en_proceso' => 'En proceso',
        'terminado' => 'Terminado',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Etapa que sigue a la actual (null si ya está en la última)
    public function siguienteEtapa(): ?string
    {
        $etapas = array_keys(self::ETAPAS);
        $posicion = array_search($this->stage, $etapas);

        return $etapas[$posicion + 1] ?? null;
    }

    // Métodos del diagrama

    // Empieza el trabajo: registra la fecha de inicio y el pedido pasa a "En producción"
    public function iniciarProduccion(): void
    {
        $this->update([
            'status' => 'en_proceso',
            'start_date' => $this->start_date ?? now(),
        ]);

        if ($this->order->status === 'pendiente') {
            $this->order->actualizarEstado('en_produccion');
        }
    }

    // Pasa a la siguiente etapa del taller y guarda la nota del avance
    public function actualizarAvance(?string $observacion = null): void
    {
        $this->stage = $this->siguienteEtapa() ?? $this->stage;
        if ($observacion) {
            $this->observation = trim($this->observation . "\n" . now()->format('d/m/Y') . ' - ' . self::ETAPAS[$this->stage] . ': ' . $observacion);
        }
        $this->save();
    }

    // Termina el trabajo; si el pedido ya no tiene producciones abiertas, el pedido queda "Terminado"
    public function marcarTerminado(): void
    {
        $this->update([
            'status' => 'terminado',
            'end_date' => now(),
        ]);

        $abiertas = $this->order->productions()->where('status', '!=', 'terminado')->exists();
        if (! $abiertas && in_array($this->order->status, ['pendiente', 'en_produccion'])) {
            $this->order->actualizarEstado('terminado');
        }
    }
}
