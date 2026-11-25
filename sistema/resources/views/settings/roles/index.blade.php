@extends('template.administrador')

@section('title', 'ROLES')
@section('name_section', 'ROLES')
@section('description', 'Listado de roles del sistema')
@section('item_section', 'Inicio')
@section('route_section', route('roles.index'))
@section('section', 'Roles')
@section('module', 'Configuraciones')

@section('content')
{{-- Mensajes flash: ok (verde) y error (rojo) --}}
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-salvia"><i class="bi bi-shield-check"></i></span>
    <div>
      <h3>Roles registrados</h3>
      <p>Total de registros encontrados: {{ $roles->count() }}</p>
    </div>
    <a href="{{ route('roles.create') }}" class="btn btn-primary ms-auto">
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
            <th>Descripción</th>
            <th class="text-center">Usuarios</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roles as $role)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $role->name }}</td>
              <td>{{ $role->description ?? '—' }}</td>
              <td class="text-center">{{ $role->users_count }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('roles.destroy', $role) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar este rol?');">
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
              <td colspan="5" class="text-center text-muted py-4">No hay roles registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
