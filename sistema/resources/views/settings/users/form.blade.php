{{-- Formulario compartido por create y edit --}}
<div class="row g-3">
  <div class="col-md-6">
    <label for="name" class="form-label">Nombre completo</label>
    <input type="text" name="name" id="name" maxlength="255" placeholder="Nombres y apellidos"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $user->name ?? '') }}">
    @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label for="role_id" class="form-label">Rol</label>
    {{-- Clave foránea: se guarda role_id, se muestra el nombre del rol --}}
    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror">
      <option value="">-- Seleccione --</option>
      @foreach ($roles as $role)
        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id ?? '') == $role->id)>
          {{ $role->name }}
        </option>
      @endforeach
    </select>
    @error('role_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="dni" class="form-label">DNI</label>
    <input type="text" name="dni" id="dni" maxlength="8" inputmode="numeric" placeholder="8 dígitos"
           class="form-control @error('dni') is-invalid @enderror"
           value="{{ old('dni', $user->dni ?? '') }}">
    @error('dni')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-8">
    <label for="email" class="form-label">Correo electrónico</label>
    <input type="email" name="email" id="email" maxlength="255" placeholder="usuario@delitex.pe"
           class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $user->email ?? '') }}">
    @error('email')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    <label for="password" class="form-label">
      Contraseña
      @isset($user)
        <small class="text-muted">(déjela vacía para no cambiarla)</small>
      @endisset
    </label>
    {{-- Nunca se rellena con value: la contraseña guardada está encriptada --}}
    <input type="password" name="password" id="password" autocomplete="new-password"
           class="form-control @error('password') is-invalid @enderror">
    @error('password')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-6">
    {{-- La regla 'confirmed' compara password con password_confirmation --}}
    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
           class="form-control">
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
  <a href="{{ route('users.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>
