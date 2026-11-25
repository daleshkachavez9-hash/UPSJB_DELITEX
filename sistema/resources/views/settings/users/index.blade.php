@extends('template.administrador')

@section('title', 'USUARIOS')
@section('name_section', 'USUARIOS')
@section('description', 'Listado de usuarios del sistema')
@section('item_section', 'Inicio')
@section('route_section', route('users.index'))
@section('section', 'Usuarios')
@section('module', 'Configuraciones')

@section('content')
{{-- Mensajes flash: ok (verde) y error (rojo) --}}
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-azul"><i class="bi bi-person-gear"></i></span>
    <div>
      <h3>Usuarios registrados</h3>
      <p>Total de registros encontrados: {{ $users->count() }}</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-person-plus me-1"></i> Nuevo registro
    </a>
  </div>
  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Correo</th>
            <th>Rol</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->dni }}</td>
              <td>{{ $user->email }}</td>
              {{-- Gracias a with('role') esto no hace una consulta por fila --}}
              <td>{{ $user->role->name }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('users.destroy', $user) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar este usuario?');">
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
              <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
