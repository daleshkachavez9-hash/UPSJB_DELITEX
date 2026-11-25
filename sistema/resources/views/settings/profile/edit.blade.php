@extends('template.administrador')

@section('title', 'MI PERFIL')
@section('name_section', 'MI PERFIL')
@section('description', 'Tus datos y tu contraseña')
@section('item_section', 'Mi perfil')
@section('route_section', route('profile.edit'))
@section('section', 'Mi perfil')
@section('module', 'Configuraciones')

@section('content')
@include('partials.alerts')

<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="text-uppercase small text-muted mb-3">Mis datos</h6>
        <form action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <label for="name" class="form-label">Nombre completo</label>
              <input type="text" name="name" id="name" maxlength="255"
                     class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $user->name) }}">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" maxlength="255"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', $user->email) }}">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-6">
              <label class="form-label">DNI</label>
              <input type="text" class="form-control" value="{{ $user->dni }}" readonly>
            </div>
            <div class="col-6">
              <label class="form-label">Rol</label>
              <input type="text" class="form-control" value="{{ $user->role->name ?? 'Sin rol' }}" readonly>
            </div>
          </div>
          <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>El DNI y el rol los cambia la administración desde Usuarios.</p>
          <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-check-lg me-1"></i> Guardar mis datos</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card h-100" id="password">
      <div class="card-body">
        <h6 class="text-uppercase small text-muted mb-3">Cambiar contraseña</h6>
        <form action="{{ route('profile.password') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <label for="current_password" class="form-label">Contraseña actual</label>
              <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                     class="form-control @error('current_password') is-invalid @enderror">
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label for="password" class="form-label">Nueva contraseña</label>
              <input type="password" name="password" id="password" autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                     class="form-control @error('password') is-invalid @enderror">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label for="password_confirmation" class="form-label">Repite la nueva contraseña</label>
              <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" class="form-control">
            </div>
          </div>
          <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-key me-1"></i> Cambiar contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
