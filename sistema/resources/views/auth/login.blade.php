<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión | DELITEX Confecciones</title>
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>

  <main class="auth-page">
    <div class="card auth-card">
      <div class="row g-0">
        <div class="col-lg-5">
          <div class="auth-side">
            <img class="brand-logo" src="{{ asset('images/logo-emblema.svg') }}" alt="Logo DELITEX">
            <p class="auth-brand">DELITEX</p>
            <p class="auth-tag">Sistema de gestión de pedidos, producción, almacén y pagos para la confección de ropa deportiva personalizada.</p>
            <img src="{{ asset('images/ilustracion-acceso.svg') }}" alt="Conjunto deportivo personalizado">
          </div>
        </div>
        <div class="col-lg-7 bg-white">
          <div class="auth-form">
            <div class="text-center">
              <span class="auth-icon soft-salvia mx-auto"><i class="bi bi-person-circle"></i></span>
              <h1>Inicio de sesión</h1>
              <p class="auth-sub">Ingresa tus credenciales para continuar</p>
            </div>

            <div class="alert alert-danger small d-none" id="loginError" role="alert"></div>

            <form id="formLogin" novalidate>
              <div class="mb-3">
                <label for="lUsuario" class="form-label">Usuario o correo</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="lUsuario" placeholder="Ingresa tu usuario" autocomplete="username" required>
                  <span class="input-group-text"><i class="bi bi-person"></i></span>
                </div>
              </div>
              <div class="mb-2">
                <label for="lClave" class="form-label">Contraseña</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="lClave" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
                  <button class="input-group-text" type="button" data-ver-clave="lClave" aria-label="Mostrar contraseña"><i class="bi bi-eye-slash"></i></button>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="lRecordar">
                  <label class="form-check-label small" for="lRecordar">Recordar usuario</label>
                </div>
                <a href="#" class="small fw-semibold">¿Olvidaste tu contraseña?</a>
              </div>
              <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

