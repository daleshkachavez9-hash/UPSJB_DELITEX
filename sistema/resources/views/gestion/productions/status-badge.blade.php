{{-- Etiqueta de color según el estado de la producción --}}
@php
  $colores = [
    'pendiente' => 'bg-secondary',
    'en_proceso' => 'bg-warning text-dark',
    'terminado' => 'bg-success',
  ];
@endphp
<span class="badge {{ $colores[$status] ?? 'bg-secondary' }}">{{ \App\Models\Production::ESTADOS[$status] ?? ucfirst($status) }}</span>
