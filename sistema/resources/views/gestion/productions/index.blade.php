@extends('template.administrador')

@section('title', 'PRODUCCIÓN')
@section('name_section', 'PRODUCCIÓN')
@section('description', 'Trabajos del taller y etapas de cada pedido')
@section('item_section', 'Inicio')
@section('route_section', route('productions.index'))
@section('section', 'Producción')
@section('module', 'Gestión')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="module-head">
    <span class="module-icon soft-amarillo"><i class="bi bi-scissors"></i></span>
    <div>
      <h3>Producción registrada</h3>
      <p>Total de registros encontrados: {{ $productions->count() }}</p>
    </div>
    <a href="{{ route('productions.create') }}" class="btn btn-primary ms-auto">
      <i class="bi bi-plus-lg me-1"></i> Nueva producción
    </a>
  </div>

  {{-- Filtros por GET: quedan en la URL (?search=...&stage=...&status=...) --}}
  <div class="card-body pb-0">
    <form action="{{ route('productions.index') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-12 col-md-5">
        <input type="text" name="search" class="form-control" placeholder="Buscar por Nº de pedido, DNI o RUC, o nombre del cliente"
               value="{{ request('search') }}">
      </div>
      <div class="col-12 col-md-3">
        <select name="stage" class="form-select">
          <option value="">Todas las etapas</option>
          @foreach ($etapas as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('stage') === $valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-2">
        <select name="status" class="form-select">
          <option value="">Todos los estados</option>
          @foreach ($estados as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('status') === $valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-outline-primary w-100" title="Filtrar"><i class="bi bi-search"></i></button>
        <a href="{{ route('productions.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>

  <div class="card-body px-0 pb-0">
    <div class="table-responsive">
      <table class="table table-hover table-delitex">
        <thead>
          <tr>
            <th class="text-center">Nº</th>
            <th class="text-center">Pedido</th>
            <th>Cliente</th>
            <th>Etapa</th>
            <th>Responsable</th>
            <th class="text-center">Inicio</th>
            <th class="text-center">Fin</th>
            <th class="text-center">Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($productions as $production)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td class="text-center">
                @can('modulo', 'seguimiento')<a href="{{ route('orders.show', $production->order) }}">Nº {{ str_pad($production->order_id, 6, '0', STR_PAD_LEFT) }}</a>@else Nº {{ str_pad($production->order_id, 6, '0', STR_PAD_LEFT) }}@endcan
              </td>
              <td>{{ $production->order->customer->name }}</td>
              <td>
                {{ $etapas[$production->stage] ?? $production->stage }}
                @if ($production->observation)
                  <i class="bi bi-chat-left-text text-muted ms-1" title="{{ $production->observation }}"></i>
                @endif
              </td>
              <td>{{ $production->user->name }}</td>
              <td class="text-center">{{ $production->start_date?->format('d/m/Y') ?? '—' }}</td>
              <td class="text-center">{{ $production->end_date?->format('d/m/Y') ?? '—' }}</td>
              <td class="text-center">@include('gestion.productions.status-badge', ['status' => $production->status])</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    @can('modulo', 'seguimiento')
                      <li>
                        <a class="dropdown-item" href="{{ route('orders.show', $production->order) }}"><i class="bi bi-eye"></i> Ver pedido</a>
                      </li>
                    @endcan
                    @if ($production->status === 'pendiente')
                      <li>
                        {{-- Método del diagrama: iniciarProduccion() --}}
                        <form method="POST" action="{{ route('productions.start', $production) }}">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="dropdown-item"><i class="bi bi-play-circle"></i> Iniciar producción</button>
                        </form>
                      </li>
                    @endif
                    @if ($production->status === 'en_proceso')
                      @if ($production->siguienteEtapa())
                        <li>
                          {{-- Método del diagrama: actualizarAvance() --}}
                          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalAvance"
                                  data-action="{{ route('productions.advance', $production) }}"
                                  data-siguiente="{{ $etapas[$production->siguienteEtapa()] }}">
                            <i class="bi bi-skip-forward-circle"></i> Pasar a {{ $etapas[$production->siguienteEtapa()] }}
                          </button>
                        </li>
                      @endif
                      <li>
                        {{-- Método del diagrama: marcarTerminado() --}}
                        <form method="POST" action="{{ route('productions.finish', $production) }}"
                              onsubmit="return confirm('¿Marcar esta producción como terminada?');">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="dropdown-item"><i class="bi bi-check2-circle"></i> Marcar terminado</button>
                        </form>
                      </li>
                    @endif
                    @if ($production->status !== 'terminado')
                      <li>
                        <a class="dropdown-item" href="{{ route('productions.edit', $production) }}"><i class="bi bi-pencil-square"></i> Editar</a>
                      </li>
                    @endif
                    @if ($production->status === 'pendiente')
                      <li>
                        <form method="POST" action="{{ route('productions.destroy', $production) }}"
                              onsubmit="return confirm('¿Seguro que desea eliminar esta producción?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Eliminar</button>
                        </form>
                      </li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-4">No se encontraron trabajos de producción.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Ventana para registrar el avance a la siguiente etapa --}}
<div class="modal fade" id="modalAvance" tabindex="-1" aria-labelledby="modalAvanceTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" class="modal-content" id="formAvance">
      @csrf
      @method('PATCH')
      <div class="modal-header">
        <h5 class="modal-title" id="modalAvanceTitulo">Pasar a <span id="avanceEtapa"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <label for="avanceObservacion" class="form-label">Nota del avance <small class="text-muted">(opcional)</small></label>
        <textarea name="observation" id="avanceObservacion" rows="3" maxlength="500" class="form-control"
                  placeholder="Ej.: 20 polos cortados, se pasan a costura"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar avance</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Lleva a la ventana la ruta y el nombre de la etapa del trabajo elegido
  document.getElementById('modalAvance').addEventListener('show.bs.modal', e => {
    document.getElementById('formAvance').action = e.relatedTarget.dataset.action;
    document.getElementById('avanceEtapa').textContent = e.relatedTarget.dataset.siguiente;
    document.getElementById('avanceObservacion').value = '';
  });
</script>
@endpush
