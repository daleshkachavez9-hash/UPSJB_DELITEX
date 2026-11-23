@extends('template.administrador')

@section('title', 'PEDIDOS')
@section('name_section', 'PEDIDOS')
@section('description', 'Detalle y seguimiento del pedido')
@section('item_section', 'Detalle')
@section('route_section', route('orders.index'))
@section('section', 'Pedidos')
@section('module', 'Gestión')

@section('content')
@include('partials.alerts')

<div class="card mb-4">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
      <h5 class="mb-0 me-2">Pedido Nº {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h5>
      @include('gestion.orders.status-badge', ['status' => $order->status])
      <div class="ms-auto d-flex flex-wrap gap-2">
        @if ($order->status !== 'entregado')
          <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-dark btn-sm"><i class="bi bi-pencil-square me-1"></i> Modificar</a>
        @endif
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Cliente</small>
        <span class="fw-semibold">{{ $order->customer->name }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">DNI</small>
        <span class="fw-semibold">{{ $order->customer->dni }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Fecha del pedido</small>
        <span class="fw-semibold">{{ $order->order_date->format('d/m/Y') }}</span>
      </div>
      @if ($order->description)
        <div class="col-12 mb-2">
          <small class="text-muted d-block">Descripción</small>
          <span>{{ $order->description }}</span>
        </div>
      @endif
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th class="small text-uppercase text-center">Nº</th>
            <th class="small text-uppercase">Prenda</th>
            <th class="small text-uppercase text-center">Talla</th>
            <th class="small text-uppercase text-center">Color</th>
            <th class="small text-uppercase text-center">Cantidad</th>
            <th class="small text-uppercase text-end">Precio unit.</th>
            <th class="small text-uppercase text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->details as $detail)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $detail->product->name ?? 'Prenda personalizada' }}</td>
              <td class="text-center">{{ $detail->size ?? '—' }}</td>
              <td class="text-center">{{ $detail->color ?? '—' }}</td>
              <td class="text-center">{{ $detail->quantity }}</td>
              <td class="text-end">S/ {{ number_format($detail->unit_price, 2) }}</td>
              <td class="text-end">S/ {{ number_format($detail->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="6" class="text-end fw-semibold">Total</td>
            <td class="text-end fw-bold">S/ {{ number_format($order->total, 2) }}</td>
          </tr>
          <tr>
            <td colspan="6" class="text-end">Pagado</td>
            <td class="text-end">S/ {{ number_format($pagado, 2) }}</td>
          </tr>
          <tr>
            <td colspan="6" class="text-end fw-semibold">Saldo</td>
            <td class="text-end fw-bold {{ $saldo > 0 ? 'text-danger' : 'text-success' }}">S/ {{ number_format($saldo, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    @if ($order->status !== 'entregado')
      {{-- Cambiar la etapa del pedido (método del diagrama: actualizarEstado) --}}
      <form action="{{ route('orders.status', $order) }}" method="POST" class="row g-2 align-items-center mt-2">
        @csrf
        @method('PATCH')
        <div class="col-auto"><label for="status" class="col-form-label small">Etapa del pedido</label></div>
        <div class="col-auto">
          <select name="status" id="status" class="form-select form-select-sm">
            @foreach ($estados as $valor => $texto)
              @continue($valor === 'entregado')
              <option value="{{ $valor }}" @selected($order->status === $valor)>{{ $texto }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Actualizar etapa</button></div>
        <div class="col-12"><small class="text-muted">El pedido pasa a «Entregado» al confirmar su entrega.</small></div>
      </form>
    @endif
  </div>
</div>

<div class="row g-4">
  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="text-uppercase small text-muted mb-3">Pagos del pedido</h6>
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th class="small text-uppercase">Fecha</th>
              <th class="small text-uppercase">Tipo</th>
              <th class="small text-uppercase text-end">Monto</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($order->payments as $payment)
              <tr>
                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                <td>{{ $payment->payment_type }}</td>
                <td class="text-end">S/ {{ number_format($payment->amount, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-muted small">Todavía no hay pagos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="text-uppercase small text-muted mb-3">Entrega</h6>
        @if ($order->delivery)
          <p class="mb-1">
            Estado:
            <span class="badge {{ $order->delivery->status === 'entregado' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($order->delivery->status) }}</span>
          </p>
          <p class="mb-1">Fecha: {{ $order->delivery->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</p>
          @if ($order->delivery->observation)
            <p class="mb-0 small text-muted">{{ $order->delivery->observation }}</p>
          @endif
        @else
          <p class="text-muted small mb-0">La entrega todavía no se ha programado.</p>
        @endif
        @if ($order->sale)
          <hr>
          <p class="mb-0 small">Venta generada: <a href="{{ route('sales.show', $order->sale) }}">Nº {{ str_pad($order->sale->id, 6, '0', STR_PAD_LEFT) }}</a></p>
        @endif
      </div>
    </div>
  </div>
</div>

<a href="{{ route('orders.index') }}" class="btn btn-outline-dark mt-4">Volver</a>
@endsection
