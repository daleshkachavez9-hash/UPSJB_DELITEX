@extends('template.administrador')

@section('title', 'ENTREGAS')
@section('name_section', 'ENTREGAS')
@section('description', 'Programar la entrega de un pedido')
@section('item_section', 'Nueva entrega')
@section('route_section', route('deliveries.index'))
@section('section', 'Entregas')
@section('module', 'Gestión')

@php
  $saldoDe = fn ($o) => round((float) $o->total - (float) $o->payments_sum_amount, 2);
@endphp

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('deliveries.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-7">
          <label for="order_label" class="form-label">Pedido</label>
          <div class="input-group has-validation">
            <input type="text" id="order_label" readonly placeholder="Busque por Nº de pedido, DNI o nombre"
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
              Total S/ {{ number_format($pedidoElegido->total, 2) }} · Saldo S/ {{ number_format($saldoDe($pedidoElegido), 2) }}
            @endif
          </small>
        </div>

        <div class="col-md-5">
          <label for="delivery_date" class="form-label">Fecha programada <small class="text-muted">(opcional)</small></label>
          <input type="date" name="delivery_date" id="delivery_date"
                 class="form-control @error('delivery_date') is-invalid @enderror"
                 value="{{ old('delivery_date') }}">
          @error('delivery_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <label for="observation" class="form-label">Observación</label>
          <textarea name="observation" id="observation" rows="2" maxlength="500"
                    class="form-control @error('observation') is-invalid @enderror">{{ old('observation') }}</textarea>
          @error('observation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>La entrega se confirma desde el listado y solo si el pedido está pagado por completo.</p>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Programar entrega</button>
        <a href="{{ route('deliveries.index') }}" class="btn btn-outline-dark">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@include('partials.modal-buscar', [
  'id' => 'modalPedido',
  'titulo' => 'Buscar pedido',
  'placeholder' => 'Escriba el Nº de pedido, el DNI o el nombre del cliente',
  'columnas' => ['Pedido', 'Cliente', 'DNI', 'Estado', 'Saldo'],
  'filas' => $orders->map(fn ($o) => [
    'id' => $o->id,
    'texto' => str_pad($o->id, 6, '0', STR_PAD_LEFT) . ' ' . $o->customer->dni . ' ' . $o->customer->name,
    'celdas' => ['Nº ' . str_pad($o->id, 6, '0', STR_PAD_LEFT), $o->customer->name, $o->customer->dni, \App\Models\Order::ESTADOS[$o->status] ?? $o->status, 'S/ ' . number_format($saldoDe($o), 2)],
    'datos' => [
      'label' => 'Nº ' . str_pad($o->id, 6, '0', STR_PAD_LEFT) . ' - ' . $o->customer->name,
      'info' => 'Total S/ ' . number_format($o->total, 2) . ' · Saldo S/ ' . number_format($saldoDe($o), 2),
    ],
  ])->all(),
])
@endsection

@push('scripts')
<script src="{{ asset('js/buscador.js') }}"></script>
<script>
  document.getElementById('modalPedido').addEventListener('buscador:elegido', e => {
    document.getElementById('order_id').value = e.detail.id;
    document.getElementById('order_label').value = e.detail.label;
    document.getElementById('order_info').textContent = e.detail.info;
  });
</script>
@endpush
