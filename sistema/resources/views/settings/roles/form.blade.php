{{-- Formulario compartido por create y edit --}}
<div class="row g-3">
  <div class="col-md-5">
    <label for="name" class="form-label">Nombre del rol</label>
    <input type="text" name="name" id="name" maxlength="50" placeholder="Ej. Administradora"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $role->name ?? '') }}">
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-7">
    <label for="description" class="form-label">Descripción</label>
    <input type="text" name="description" id="description" maxlength="255" placeholder="Qué puede hacer este rol"
           class="form-control @error('description') is-invalid @enderror"
           value="{{ old('description', $role->description ?? '') }}">
    @error('description')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('roles.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>
