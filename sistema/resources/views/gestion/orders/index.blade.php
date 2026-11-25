@extends('template.administrador')

@section('title', 'PEDIDOS')
@section('name_section', 'PEDIDOS')
@section('description', 'Pedidos personalizados de los clientes')
@section('item_section', 'Inicio')
@section('route_section', route('orders.index'))
@section('section', 'Pedidos')
@section('module', 'Gestión')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-azul"><i class="bi bi-clipboard-check"></i></span>
    <div>
      <h3>Pedidos registrados</h3>
      <p>Total de registros encontrados: {{ $orders->count() }}</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nuevo pedido
    </a>
  </div>

  {{-- Filtros por GET: quedan en la URL (?search=...&status=...) --}}
  <div class="card-body pb-0">
    <form action="{{ route('orders.index') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Buscar por Nº de pedido, DNI o nombre del cliente"
               value="{{ request('search') }}">
      </div>
      <div class="col-12 col-md-3">
        <select name="status" class="form-select">
          <option value="">Todos los estados</option>
          @foreach ($estados as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('status') === $valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-outline-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>

  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Pedido</th>
            <th class="text-center">Fecha</th>
            <th>Cliente</th>
            <th class="text-end">Total</th>
            <th class="text-end">Pagado</th>
            <th class="text-end">Saldo</th>
            <th class="text-center">Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            @php $saldo = (float) $order->total - (float) $order->payments_sum_amount; @endphp
            <tr>
              <td class="text-center">Nº {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
              <td class="text-center">{{ $order->order_date->format('d/m/Y') }}</td>
              <td>{{ $order->customer->name }}<br><small class="text-muted">DNI {{ $order->customer->dni }}</small></td>
              <td class="text-end">S/ {{ number_format($order->total, 2) }}</td>
              <td class="text-end">S/ {{ number_format($order->payments_sum_amount ?? 0, 2) }}</td>
              <td class="text-end {{ $saldo > 0 ? 'text-danger fw-semibold' : '' }}">S/ {{ number_format($saldo, 2) }}</td>
              <td class="text-center">
                @include('gestion.orders.status-badge', ['status' => $order->status])
              </td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('orders.show', $order) }}"><i class="bi bi-eye"></i> Ver detalle</a>
                    </li>
                    @if ($order->status !== 'entregado')
                      <li>
                        <a class="dropdown-item" href="{{ route('orders.edit', $order) }}"><i class="bi bi-pencil-square"></i> Modificar</a>
                      </li>
                    @endif
                    <li>
                      <form method="POST" action="{{ route('orders.destroy', $order) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar este pedido?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Eliminar</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No se encontraron pedidos.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
