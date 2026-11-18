@extends('template.administrador')

@section('title', 'CLIENTES')
@section('name_section', 'CLIENTES')
@section('description', 'Listado de clientes')
@section('item_section', 'Inicio')
@section('section', 'Clientes')
@section('module', 'Gestión')

@section('content')
{{-- Mensajes flash: los envían store(), update() y destroy() --}}
@if (session('ok'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('ok') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-lavanda"><i class="bi bi-people"></i></span>
    <div>
      <h3>Clientes registrados</h3>
      <p>Total de registros encontrados: {{ $customers->count() }}</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-person-plus me-1"></i> Nuevo registro
    </a>
  </div>
  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th>DNI</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $customer)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $customer->dni }}</td>
              <td>{{ $customer->name }}</td>
              <td>{{ $customer->phone }}</td>
              <td>{{ $customer->address }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('customers.edit', $customer->id) }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </a>
                    </li>
                    <li>
                      {{-- Eliminar: formulario DELETE (un enlace solo envía GET) --}}
                      <form method="POST" action="{{ route('customers.destroy', $customer->id) }}"
                            onsubmit="return confirm('¿Eliminar este registro?');">
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
              <td colspan="6" class="text-center text-muted py-4">No hay clientes registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
