@extends('template.administrador')

@section('title', 'INVENTARIO')
@section('name_section', 'INVENTARIO')
@section('description', 'Stock actual y stock mínimo de cada producto')
@section('item_section', 'Inicio')
@section('route_section', route('inventories.index'))
@section('section', 'Inventario')
@section('module', 'Gestión')

@section('content')
{{-- Mensajes flash: ok (verde) y error (rojo) --}}
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-azul"><i class="bi bi-box-seam"></i></span>
    <div>
      <h3>Control de inventario</h3>
      <p>Total de registros encontrados: {{ $inventories->count() }}</p>
    </div>
    <a href="{{ route('inventories.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nuevo registro
    </a>
  </div>

  {{-- Filtros por GET: el buscador y "Solo alertas" quedan en la URL --}}
  <div class="card-body pb-0">
    <form action="{{ route('inventories.index') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Buscar por producto"
               value="{{ request('search') }}">
      </div>
      <div class="col-6 col-md-3">
        <div class="form-check">
          <input type="checkbox" name="alerts" value="1" id="alerts" class="form-check-input"
                 @checked(request('alerts'))>
          <label for="alerts" class="form-check-label small">Solo alertas de stock</label>
        </div>
      </div>
      <div class="col-6 col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-outline-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button>
        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>

  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th>Producto</th>
            <th>Categoría</th>
            <th class="text-center">Stock actual</th>
            <th class="text-center">Stock mínimo</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Actualización</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($inventories as $inventory)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $inventory->product->name }}</td>
              <td>{{ $inventory->product->category->name }}</td>
              <td class="text-center">{{ rtrim(rtrim($inventory->current_stock, '0'), '.') }} {{ $inventory->product->unit }}</td>
              <td class="text-center">{{ rtrim(rtrim($inventory->minimum_stock, '0'), '.') }} {{ $inventory->product->unit }}</td>
              <td class="text-center">
                {{-- Método del diagrama: generarAlerta() --}}
                @if ($inventory->generarAlerta())
                  <span class="badge bg-danger">Reponer</span>
                @else
                  <span class="badge bg-success">Suficiente</span>
                @endif
              </td>
              <td class="text-center">{{ $inventory->last_update?->format('d/m/Y') ?? '—' }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('inventories.edit', $inventory) }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('inventories.destroy', $inventory) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar este registro?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                          <i class="bi bi-trash"></i> Eliminar
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No se encontraron registros de inventario.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
