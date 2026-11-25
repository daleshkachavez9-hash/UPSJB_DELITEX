@extends('template.administrador')

@section('title', 'REPORTES')
@section('name_section', 'REPORTES')
@section('description', 'Reportes de ventas, pedidos, pagos e inventario')
@section('item_section', 'Inicio')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-azul"><i class="bi bi-file-earmark-bar-graph"></i></span>
    <div>
      <h3>Reportes generados</h3>
      <p>Total de registros encontrados: {{ $reports->count() }}</p>
    </div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nuevo reporte
    </a>
  </div>

  {{-- Filtros por GET: quedan en la URL (?type=...&user_id=...) --}}
  <div class="card-body pb-0">
    <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-12 col-md-4">
        <select name="type" class="form-select">
          <option value="">Todos los tipos</option>
          @foreach ($tipos as $tipo)
            <option value="{{ $tipo }}" @selected(request('type') === $tipo)>{{ $tipo }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-5">
        <select name="user_id" class="form-select">
          <option value="">Todos los usuarios</option>
          @foreach ($users as $user)
            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-outline-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>

  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th class="text-center">Fecha</th>
            <th>Tipo</th>
            <th>Generado por</th>
            <th>Resumen</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($reports as $report)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">{{ $report->generated_date->format('d/m/Y') }}</td>
              <td>{{ $report->type }}</td>
              <td>{{ $report->user->name }}</td>
              {{-- Solo la primera línea del contenido --}}
              <td class="text-muted">{{ Str::limit(strtok((string) $report->content, "\n"), 60) }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('reports.show', $report) }}"><i class="bi bi-eye"></i> Ver reporte</a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('reports.edit', $report) }}"><i class="bi bi-pencil-square"></i> Editar</a>
                    </li>
                    <li>
                      <form method="POST" action="{{ route('reports.destroy', $report) }}"
                            onsubmit="return confirm('¿Seguro que desea eliminar este reporte?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Eliminar</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No se encontraron reportes.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
