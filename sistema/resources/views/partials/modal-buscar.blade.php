{{-- Ventana de búsqueda reutilizable.
     Recibe: $id, $titulo, $placeholder, $columnas y $filas.
     Cada fila trae 'id', 'texto' (lo que se busca), 'celdas' (lo que se ve) y 'datos' (se devuelven al elegir). --}}
<div class="modal fade modal-buscar" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $id }}Label">{{ $titulo }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="search" class="form-control buscar-texto" placeholder="{{ $placeholder }}" autocomplete="off">
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead>
              <tr>
                @foreach ($columnas as $columna)
                  <th class="small text-uppercase">{{ $columna }}</th>
                @endforeach
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($filas as $fila)
                <tr data-id="{{ $fila['id'] }}" data-texto="{{ $fila['texto'] }}"
                    @foreach ($fila['datos'] ?? [] as $clave => $valor) data-{{ $clave }}="{{ $valor }}" @endforeach>
                  @foreach ($fila['celdas'] as $celda)
                    <td>{{ $celda }}</td>
                  @endforeach
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary buscar-elegir">Seleccionar</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <p class="text-center text-muted small py-3 mb-0 buscar-vacio {{ count($filas) ? 'd-none' : '' }}">No se encontraron resultados.</p>
        </div>
      </div>
    </div>
  </div>
</div>
