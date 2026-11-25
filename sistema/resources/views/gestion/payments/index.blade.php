@extends('template.administrador')

@section('title', 'PAGOS')
@section('name_section', 'PAGOS')
@section('description', 'Adelantos y pagos de los pedidos')
@section('item_section', 'Inicio')
@section('route_section', route('payments.index'))
@section('section', 'Pagos')
@section('module', 'Gestión')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-salvia"><i class="bi bi-cash-coin"></i></span>
    <div>
      <h3>Pagos registrados</h3>
      <p>Total de registros encontrados: {{ $payments->count() }}</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Registrar pago
    </a>
  </div>
  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Pedido</th>
            <th>Cliente</th>
            <th>Forma de pago</th>
            <th class="text-end">Monto</th>
            <th class="text-center">Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($payments as $payment)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">{{ $payment->payment_date->format('d/m/Y') }}</td>
              <td class="text-center">
                @can('modulo', 'seguimiento')<a href="{{ route('orders.show', $payment->order) }}">Nº {{ str_pad($payment->order_id, 6, '0', STR_PAD_LEFT) }}</a>@else Nº {{ str_pad($payment->order_id, 6, '0', STR_PAD_LEFT) }}@endcan
              </td>
              <td>{{ $payment->order->customer->name }}</td>
              <td>{{ $payment->payment_type }}</td>
              <td class="text-end">S/ {{ number_format($payment->amount, 2) }}</td>
              <td class="text-center">
                {{-- Adelanto: todavía queda saldo. Cancelación: con este pago el pedido quedó pagado. --}}
                <span class="badge {{ $payment->status === 'cancelacion' ? 'bg-success' : 'bg-warning text-dark' }}"
                      title="{{ $payment->status === 'cancelacion' ? 'Con este pago el pedido quedó pagado' : 'Después de este pago todavía quedaba saldo' }}">
                  {{ \App\Models\Payment::ESTADOS[$payment->status] ?? $payment->status }}
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
                        <a class="dropdown-item" href="{{ route('orders.show', $payment->order) }}"><i class="bi bi-eye"></i> Ver pedido</a>
                      </li>
                    @endcan
                    <li>
                      <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                            onsubmit="return confirm('¿Seguro que desea anular este pago?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle"></i> Anular</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No hay pagos registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
