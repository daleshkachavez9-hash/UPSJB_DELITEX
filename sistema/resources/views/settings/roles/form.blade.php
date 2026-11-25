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

  {{-- Permisos: módulos a los que pueden entrar los usuarios con este rol --}}
  <div class="col-12">
    <label class="form-label d-block">Módulos permitidos</label>
    @php $marcados = old('modules', $role->modules ?? []); @endphp
    <div class="row g-2 @error('modules') is-invalid @enderror">
      @foreach (\App\Models\Role::MODULOS as $valor => $texto)
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $valor }}" id="mod_{{ $valor }}"
                   @checked(in_array($valor, $marcados))>
            <label class="form-check-label" for="mod_{{ $valor }}">{{ $texto }}</label>
          </div>
        </div>
      @endforeach
    </div>
    @error('modules')
      <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="text-muted">El dashboard y Mi perfil están disponibles para todos los roles.</small>
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('roles.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>
