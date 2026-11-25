{{-- Etiqueta de color según la etapa del pedido --}}
@php
  $colores = [
    'pendiente' => 'bg-secondary',
    'en_produccion' => 'bg-warning text-dark',
    'terminado' => 'bg-primary',
    'entregado' => 'bg-success',
  ];
@endphp
<span class="badge {{ $colores[$status] ?? 'bg-secondary' }}">{{ \App\Models\Order::ESTADOS[$status] ?? ucfirst($status) }}</span>
