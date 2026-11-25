@extends('template.administrador')

@section('title', 'PRODUCCIÓN')
@section('name_section', 'PRODUCCIÓN')
@section('description', 'Actualizar trabajo de producción')
@section('item_section', 'Actualizar registro')
@section('route_section', route('productions.index'))
@section('section', 'Producción')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('productions.update', $production) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Pedido</label>
          <input type="text" class="form-control" readonly
                 value="Nº {{ str_pad($production->order_id, 6, '0', STR_PAD_LEFT) }} - {{ $production->order->customer->name }}">
        </div>

        <div class="col-md-4">
          <label for="start_date" class="form-label">Fecha de inicio <small class="text-muted">(opcional)</small></label>
          <input type="date" name="start_date" id="start_date" max="{{ now()->format('Y-m-d') }}"
                 class="form-control @error('start_date') is-invalid @enderror"
                 value="{{ old('start_date', $production->start_date?->format('Y-m-d')) }}">
          @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        @include('gestion.productions.form')
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
        <a href="{{ route('productions.index') }}" class="btn btn-outline-dark">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
