@extends('template.administrador')

@section('title', 'VENTAS')
@section('name_section', 'VENTAS')
@section('description', 'Listado de ventas registradas')
@section('item_section', 'Inicio')
@section('route_section', route('sales.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@section('content')
{{-- Mensajes flash: ok (verde) y error (rojo) --}}
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-rosa"><i class="bi bi-cart3"></i></span>
    <div>
      <h3>Ventas registradas</h3>
      <p>Total de registros encontrados: {{ $sales->count() }}</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nueva venta
    </a>
  </div>
  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th class="text-center">Fecha</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th class="text-end">Total</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($sales as $sale)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">{{ $sale->date->format('d/m/Y H:i') }}</td>
              <td>{{ $sale->customer->name }}</td>
              <td>{{ $sale->user->name }}</td>
              <td class="text-end">S/ {{ number_format($sale->total, 2) }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('sales.show', $sale) }}">
                        <i class="bi bi-eye"></i> Ver detalle
                      </a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                            onsubmit="return confirm('¿Seguro que desea anular esta venta? El stock será devuelto.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                          <i class="bi bi-x-circle"></i> Anular
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No hay ventas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
