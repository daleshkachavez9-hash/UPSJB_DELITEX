@extends('template.administrador')

@section('title', 'CATEGORÍAS')
@section('name_section', 'CATEGORÍAS')
@section('description', 'Listado de categorías de productos')
@section('item_section', 'Inicio')
@section('route_section', route('categories.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
{{-- Mensajes flash: ok (verde) y error (rojo) --}}
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-amarillo"><i class="bi bi-tags"></i></span>
    <div>
      <h3>Categorías registradas</h3>
      <p>Total de registros encontrados: {{ $categories->count() }}</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nuevo registro
    </a>
  </div>
  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th class="text-center">Productos</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($categories as $category)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $category->name }}</td>
              <td><span class="badge {{ $category->type === 'prenda' ? 'bg-primary' : 'bg-secondary' }}">{{ $category->type === 'prenda' ? 'Prenda' : 'Insumo' }}</span></td>
              <td>{{ $category->description ?? '—' }}</td>
              <td class="text-center">{{ $category->products_count }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('categories.destroy', $category) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar esta categoría?');">
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
              <td colspan="6" class="text-center text-muted py-4">No hay categorías registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
