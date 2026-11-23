@extends('template.administrador')

@section('title', 'ENTREGAS')
@section('name_section', 'ENTREGAS')
@section('description', 'Actualizar entrega programada')
@section('item_section', 'Actualizar registro')
@section('route_section', route('deliveries.index'))
@section('section', 'Entregas')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('deliveries.update', $delivery) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-7">
          <label class="form-label">Pedido</label>
          <input type="text" class="form-control" readonly
                 value="Nº {{ str_pad($delivery->order_id, 6, '0', STR_PAD_LEFT) }} - {{ $delivery->order->customer->name }}">
        </div>

        <div class="col-md-5">
          <label for="delivery_date" class="form-label">Fecha programada <small class="text-muted">(opcional)</small></label>
          <input type="date" name="delivery_date" id="delivery_date"
                 class="form-control @error('delivery_date') is-invalid @enderror"
                 value="{{ old('delivery_date', $delivery->delivery_date?->format('Y-m-d')) }}">
          @error('delivery_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <label for="observation" class="form-label">Observación</label>
          <textarea name="observation" id="observation" rows="2" maxlength="500"
                    class="form-control @error('observation') is-invalid @enderror">{{ old('observation', $delivery->observation) }}</textarea>
          @error('observation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
        <a href="{{ route('deliveries.index') }}" class="btn btn-outline-dark">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
