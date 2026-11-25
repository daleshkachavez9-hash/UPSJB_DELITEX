{{-- Formulario compartido por create y edit --}}
<div class="row g-3">
  <div class="col-md-4">
    <label for="type" class="form-label">Tipo de reporte</label>
    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
      <option value="">Seleccione</option>
      @foreach ($tipos as $tipo)
        <option value="{{ $tipo }}" @selected(old('type', $report->type ?? '') === $tipo)>{{ $tipo }}</option>
      @endforeach
    </select>
    @error('type')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="user_id" class="form-label">Generado por</label>
    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
      <option value="">Seleccione</option>
      @foreach ($users as $user)
        <option value="{{ $user->id }}" @selected((string) old('user_id', $report->user_id ?? '') === (string) $user->id)>
          {{ $user->name }}{{ $user->role ? ' (' . $user->role->name . ')' : '' }}
        </option>
      @endforeach
    </select>
    @error('user_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="generated_date" class="form-label">Fecha de generación</label>
    <input type="date" name="generated_date" id="generated_date"
           class="form-control @error('generated_date') is-invalid @enderror"
           value="{{ old('generated_date', isset($report) ? $report->generated_date->format('Y-m-d') : now()->format('Y-m-d')) }}">
    @error('generated_date')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-12">
    <label for="content" class="form-label">Contenido</label>
    <textarea name="content" id="content" rows="10" maxlength="5000"
              class="form-control font-monospace @error('content') is-invalid @enderror"
              placeholder="Déjelo vacío y el sistema armará el resumen del mes con los datos registrados.">{{ old('content', $report->content ?? '') }}</textarea>
    @error('content')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">
      Ventas, pedidos y pagos se resumen por el mes de la fecha elegida; inventario muestra el stock actual.
      Puede agregar observaciones al texto generado.
    </small>
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('reports.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>
