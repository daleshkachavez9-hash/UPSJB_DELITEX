<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="DELITEX Confecciones – Sistema de gestión de pedidos, producción e inventario">
  <title>DELITEX | @yield('title')</title>
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>

  @include('partials.sidebar')
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <div class="app-main">
    @include('partials.header')

    <main class="app-content">
      <div class="page-header">
        <div>
          <h2>@yield('section')</h2>
          <p>@yield('description')</p>
        </div>
        <nav class="breadcrumb-pill" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="bi bi-house-door"></i> @yield('item_section')</a></li>
            <li class="breadcrumb-item">@yield('section')</li>
            <li class="breadcrumb-item active" aria-current="page">@yield('module')</li>
          </ol>
        </nav>
      </div>

      @yield('content')
    </main>

    <footer class="app-footer d-flex flex-wrap justify-content-between gap-2">
      <span>© {{ date('Y') }} DELITEX Confecciones · Sistema de gestión</span>
    </footer>
  </div>

  @include('partials.modal-logout')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
