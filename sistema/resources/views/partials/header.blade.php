<header class="app-topbar">
  <button class="btn-icon" id="btnMenu" type="button" aria-label="Mostrar u ocultar menú" aria-controls="sidebar">
    <i class="bi bi-list"></i>
  </button>
  <h1 class="topbar-title">@yield('name_section')</h1>

  <div class="ms-auto d-flex align-items-center gap-3">
    <span class="topbar-date d-none d-md-inline"><i class="bi bi-calendar3 me-1"></i><span id="fechaHoy"></span></span>

    <div class="dropdown">
      <button class="btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
        <i class="bi bi-bell"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-end notif-menu">
        <h6 class="dropdown-header">Notificaciones</h6>
        <p class="dropdown-item-text small text-muted mb-0">No hay notificaciones nuevas.</p>
      </div>
    </div>
  </div>
</header>
