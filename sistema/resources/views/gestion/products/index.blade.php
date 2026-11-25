@extends('template.administrador')

@section('title', 'PRODUCTOS')
@section('name_section', 'PRODUCTOS')
@section('description', 'Prendas, telas, hilos y accesorios')
@section('item_section', 'Inicio')
@section('route_section', route('products.index'))
@section('section', 'Productos')
@section('module', 'Gestión')

@section('content')
{{-- Mensajes flash: ok (verde) y error (rojo) --}}
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-lavanda"><i class="bi bi-boxes"></i></span>
    <div>
      <h3>Productos registrados</h3>
      <p>Total de registros encontrados: {{ $products->count() }}</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nuevo registro
    </a>
  </div>

  {{-- Filtros: el formulario usa GET, así los filtros viajan en la URL (?search=...&category_id=...) --}}
  <div class="card-body pb-0">
    <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-12 col-md-5">
        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre"
               value="{{ request('search') }}">
      </div>
      <div class="col-12 col-md-3">
        <select name="category_id" class="form-select">
          <option value="">Todas las categorías</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-md-2">
        <div class="form-check">
          <input type="checkbox" name="low_stock" value="1" id="low_stock" class="form-check-input"
                 @checked(request('low_stock'))>
          <label for="low_stock" class="form-check-label small">Stock bajo</label>
        </div>
      </div>
      <div class="col-6 col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-outline-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>

  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th class="text-center">Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Tipo</th>
            <th class="text-end">Precio</th>
            <th class="text-center">Stock</th>
            <th class="text-center">Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($products as $product)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">
                @if ($product->image)
                  <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                       class="rounded" style="width: 48px; height: 48px; object-fit: cover">
                @else
                  <i class="bi bi-image text-muted fs-4"></i>
                @endif
              </td>
              <td>{{ $product->name }}</td>
              {{-- Gracias a with('category') esto no hace una consulta por fila --}}
              <td>{{ $product->category->name }}</td>
              <td>{{ $product->garment_type }}</td>
              <td class="text-end">S/ {{ number_format($product->price, 2) }}</td>
              <td class="text-center">
                {{-- En rojo si el stock está bajo --}}
                <span class="badge {{ $product->quantity <= $lowStock ? 'bg-danger' : 'bg-success' }}">
                  {{ rtrim(rtrim($product->quantity, '0'), '.') }} {{ $product->unit }}
                </span>
              </td>
              <td class="text-center">
                <span class="badge {{ $product->status === 'activo' ? 'bg-primary' : 'bg-secondary' }}">{{ ucfirst($product->status) }}</span>
              </td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('products.edit', $product) }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('products.destroy', $product) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar este producto?');">
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
              <td colspan="9" class="text-center text-muted py-4">No se encontraron productos.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
