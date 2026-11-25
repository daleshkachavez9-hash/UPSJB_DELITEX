{{-- Plantilla de las páginas de error (403, 404, 419) con el estilo del sistema --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $titulo }} | DELITEX Confecciones</title>
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>
  <main class="auth-page">
    <div class="card auth-card" style="max-width: 520px;">
      <div class="auth-form text-center">
        <span class="auth-icon soft-rosa mx-auto"><i class="bi {{ $icono }}"></i></span>
        <h1>{{ $titulo }}</h1>
        <p class="auth-sub mb-4">{{ $mensaje }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary"><i class="bi bi-house me-1"></i> Volver al inicio</a>
      </div>
    </div>
  </main>
</body>
</html>
