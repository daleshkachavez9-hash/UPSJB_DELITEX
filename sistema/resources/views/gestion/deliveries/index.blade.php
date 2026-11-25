@extends('template.administrador')

@section('title', 'ENTREGAS')
@section('name_section', 'ENTREGAS')
@section('description', 'Salida de pedidos terminados')
@section('item_section', 'Inicio')
@section('route_section', route('deliveries.index'))
@section('section', 'Entregas')
@section('module', 'Gestión')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-amarillo"><i class="bi bi-truck"></i></span>
    <div>
      <h3>Entregas registradas</h3>
      <p>Total de registros encontrados: {{ $deliveries->count() }}</p>
    </div>
    <a href="{{ route('deliveries.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Programar entrega
    </a>
  </div>
  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th class="text-center">Pedido</th>
            <th>Cliente</th>
            <th class="text-center">Fecha</th>
            <th class="text-end">Saldo</th>
            <th class="text-center">Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($deliveries as $delivery)
            @php $saldo = (float) $delivery->order->total - (float) $delivery->order->payments_sum_amount; @endphp
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">
                @can('modulo', 'seguimiento')<a href="{{ route('orders.show', $delivery->order) }}">Nº {{ str_pad($delivery->order_id, 6, '0', STR_PAD_LEFT) }}</a>@else Nº {{ str_pad($delivery->order_id, 6, '0', STR_PAD_LEFT) }}@endcan
              </td>
              <td>{{ $delivery->order->customer->name }}</td>
              <td class="text-center">{{ $delivery->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</td>
              <td class="text-end {{ $saldo > 0 ? 'text-danger fw-semibold' : '' }}">S/ {{ number_format($saldo, 2) }}</td>
              <td class="text-center">
                <span class="badge {{ $delivery->status === 'entregado' ? 'bg-success' : 'bg-secondary' }}">
                  {{ $delivery->status === 'entregado' ? 'Entregado' : 'Pendiente' }}
                </span>
              </td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    @can('modulo', 'seguimiento')
                      <li>
                        <a class="dropdown-item" href="{{ route('orders.show', $delivery->order) }}"><i class="bi bi-eye"></i> Ver pedido</a>
                      </li>
                    @endcan
                    @if ($delivery->status !== 'entregado')
                      <li>
                        {{-- Método del diagrama: confirmarEntrega() (solo con el pedido pagado por completo) --}}
                        <form method="POST" action="{{ route('deliveries.confirm', $delivery) }}"
                              onsubmit="return confirm('¿Confirmar la entrega de este pedido?');">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="dropdown-item"><i class="bi bi-check2-circle"></i> Confirmar entrega</button>
                        </form>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{ route('deliveries.edit', $delivery) }}"><i class="bi bi-pencil-square"></i> Editar</a>
                      </li>
                      <li>
                        <form method="POST" action="{{ route('deliveries.destroy', $delivery) }}"
                              onsubmit="return confirm('¿Seguro que desea eliminar esta entrega?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Eliminar</button>
                        </form>
                      </li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No hay entregas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
