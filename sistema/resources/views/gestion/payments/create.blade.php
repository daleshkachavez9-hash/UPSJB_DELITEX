@extends('template.administrador')

@section('title', 'PAGOS')
@section('name_section', 'PAGOS')
@section('description', 'Registrar pago de un pedido')
@section('item_section', 'Nuevo pago')
@section('route_section', route('payments.index'))
@section('section', 'Pagos')
@section('module', 'Gestión')

@php
  $saldoDe = fn ($o) => round((float) $o->total - (float) $o->payments_sum_amount, 2);
@endphp

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('payments.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-7">
          <label for="order_label" class="form-label">Pedido</label>
          <div class="input-group has-validation">
            <input type="text" id="order_label" readonly placeholder="Busque por Nº de pedido, documento o nombre"
                   class="form-control bg-white @error('order_id') is-invalid @enderror"
                   value="{{ $pedidoElegido ? 'Nº ' . str_pad($pedidoElegido->id, 6, '0', STR_PAD_LEFT) . ' - ' . $pedidoElegido->customer->name : '' }}">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPedido">
              <i class="bi bi-search me-1"></i> Buscar
            </button>
            @error('order_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <input type="hidden" name="order_id" id="order_id" value="{{ old('order_id', $pedidoElegido?->id) }}">
          <small class="text-muted d-block mt-1" id="order_info">
            @if ($pedidoElegido)
              Total S/ {{ number_format($pedidoElegido->total, 2) }} · Pagado S/ {{ number_format($pedidoElegido->payments_sum_amount ?? 0, 2) }} · Saldo S/ {{ number_format($saldoDe($pedidoElegido), 2) }}
            @endif
          </small>
        </div>

        <div class="col-md-5">
          <label for="amount" class="form-label">Monto (S/)</label>
          <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                 class="form-control @error('amount') is-invalid @enderror"
                 value="{{ old('amount', $pedidoElegido ? number_format($saldoDe($pedidoElegido), 2, '.', '') : '') }}">
          @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_date" class="form-label">Fecha de pago</label>
          <input type="date" name="payment_date" id="payment_date" max="{{ now()->format('Y-m-d') }}"
                 class="form-control @error('payment_date') is-invalid @enderror"
                 value="{{ old('payment_date', now()->format('Y-m-d')) }}">
          @error('payment_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_type" class="form-label">Forma de pago</label>
          <select name="payment_type" id="payment_type" class="form-select @error('payment_type') is-invalid @enderror">
            @foreach ($tipos as $tipo)
              <option value="{{ $tipo }}" @selected(old('payment_type') === $tipo)>{{ $tipo }}</option>
            @endforeach
          </select>
          @error('payment_type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Registrar pago</button>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-dark">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@include('partials.modal-buscar', [
  'id' => 'modalPedido',
  'titulo' => 'Buscar pedido con saldo pendiente',
  'placeholder' => 'Escriba el Nº de pedido, el DNI o RUC, o el nombre del cliente',
  'columnas' => ['Pedido', 'Cliente', 'Documento', 'Total', 'Saldo'],
  'filas' => $orders->map(fn ($o) => [
    'id' => $o->id,
    'texto' => str_pad($o->id, 6, '0', STR_PAD_LEFT) . ' ' . $o->customer->document_number . ' ' . $o->customer->name,
    'celdas' => ['Nº ' . str_pad($o->id, 6, '0', STR_PAD_LEFT), $o->customer->name, $o->customer->documento(), 'S/ ' . number_format($o->total, 2), 'S/ ' . number_format($saldoDe($o), 2)],
    'datos' => [
      'label' => 'Nº ' . str_pad($o->id, 6, '0', STR_PAD_LEFT) . ' - ' . $o->customer->name,
      'info' => 'Total S/ ' . number_format($o->total, 2) . ' · Pagado S/ ' . number_format($o->payments_sum_amount ?? 0, 2) . ' · Saldo S/ ' . number_format($saldoDe($o), 2),
      'saldo' => number_format($saldoDe($o), 2, '.', ''),
    ],
  ])->all(),
])
@endsection

@push('scripts')
<script src="{{ asset('js/buscador.js') }}"></script>
<script>
  // Pedido elegido: muestra su saldo y lo propone como monto
  document.getElementById('modalPedido').addEventListener('buscador:elegido', e => {
    document.getElementById('order_id').value = e.detail.id;
    document.getElementById('order_label').value = e.detail.label;
    document.getElementById('order_info').textContent = e.detail.info;
    document.getElementById('amount').value = e.detail.saldo;
  });
</script>
@endpush
