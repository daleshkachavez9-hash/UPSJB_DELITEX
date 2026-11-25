@extends('template.administrador')

@section('title', 'PRODUCCIÓN')
@section('name_section', 'PRODUCCIÓN')
@section('description', 'Enviar un pedido al taller')
@section('item_section', 'Nueva producción')
@section('route_section', route('productions.index'))
@section('section', 'Producción')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('productions.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-12">
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
              {{ \App\Models\Order::ESTADOS[$pedidoElegido->status] }} · {{ $pedidoElegido->description ?? 'Sin descripción' }}
            @endif
          </small>
        </div>

        @include('gestion.productions.form', ['production' => null])

        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="start" value="1" id="start" @checked(old('start'))>
            <label class="form-check-label" for="start">Iniciar el trabajo ahora (registra la fecha de inicio de hoy)</label>
          </div>
        </div>
      </div>
      <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Solo aparecen los pedidos pendientes o en producción. Al iniciar el trabajo, el pedido pasa a «En producción».</p>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Registrar producción</button>
        <a href="{{ route('productions.index') }}" class="btn btn-outline-dark">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@include('partials.modal-buscar', [
  'id' => 'modalPedido',
  'titulo' => 'Buscar pedido',
  'placeholder' => 'Escriba el Nº de pedido, el DNI o RUC, o el nombre del cliente',
  'columnas' => ['Pedido', 'Cliente', 'Documento', 'Fecha', 'Estado'],
  'filas' => $orders->map(fn ($o) => [
    'id' => $o->id,
    'texto' => str_pad($o->id, 6, '0', STR_PAD_LEFT) . ' ' . $o->customer->document_number . ' ' . $o->customer->name,
    'celdas' => ['Nº ' . str_pad($o->id, 6, '0', STR_PAD_LEFT), $o->customer->name, $o->customer->documento(), $o->order_date->format('d/m/Y'), \App\Models\Order::ESTADOS[$o->status] ?? $o->status],
    'datos' => [
      'label' => 'Nº ' . str_pad($o->id, 6, '0', STR_PAD_LEFT) . ' - ' . $o->customer->name,
      'info' => (\App\Models\Order::ESTADOS[$o->status] ?? $o->status) . ' · ' . ($o->description ?? 'Sin descripción'),
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
