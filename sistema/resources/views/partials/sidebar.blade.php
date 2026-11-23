<aside class="app-sidebar" id="sidebar" aria-label="Menú principal">
  <a href="{{ route('dashboard.index') }}" class="sidebar-brand">
    <img class="brand-logo" src="{{ asset('images/logo-emblema.svg') }}" alt="Logo DELITEX">
    <span class="brand-name">DELITEX</span>
    <span class="brand-sub">Confecciones deportivas</span>
  </a>

  <nav class="sidebar-nav">
    <p class="nav-section">Principal</p>
    <a href="{{ route('dashboard.index') }}"
       class="sidebar-link {{ request()->is('/') ? 'active' : null }}"
       aria-current="{{ request()->is('/') ? 'page' : null }}">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>
    <a href="{{ route('reports.index') }}"
       class="sidebar-link {{ request()->is(['reports*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['reports*']) ? 'page' : null }}">
      <i class="bi bi-file-earmark-bar-graph"></i> Reportes
    </a>

    <p class="nav-section">Gestión</p>
    <a href="{{ route('customers.index') }}"
       class="sidebar-link {{ request()->is(['customers*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['customers*']) ? 'page' : null }}">
      <i class="bi bi-people"></i> Clientes
    </a>
    <a href="{{ route('orders.index') }}"
       class="sidebar-link {{ request()->is(['orders*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['orders*']) ? 'page' : null }}">
      <i class="bi bi-clipboard-check"></i> Pedidos
    </a>
    <a href="{{ route('sales.index') }}"
       class="sidebar-link {{ request()->is(['sales*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['sales*']) ? 'page' : null }}">
      <i class="bi bi-cart3"></i> Ventas
    </a>
    {{-- El seguimiento se hace desde el listado de pedidos (búsqueda por Nº o DNI) y su detalle --}}
    <a href="{{ route('orders.index') }}" class="sidebar-link"><i class="bi bi-signpost-split"></i> Seguimiento de pedidos</a>
    <a href="#" class="sidebar-link"><i class="bi bi-scissors"></i> Producción</a>
    <a href="{{ route('products.index') }}"
       class="sidebar-link {{ request()->is(['products*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['products*']) ? 'page' : null }}">
      <i class="bi bi-boxes"></i> Productos
    </a>
    <a href="{{ route('inventories.index') }}"
       class="sidebar-link {{ request()->is(['inventories*']) && !request()->boolean('alerts') ? 'active' : null }}"
       aria-current="{{ request()->is(['inventories*']) && !request()->boolean('alerts') ? 'page' : null }}">
      <i class="bi bi-box-seam"></i> Inventario
    </a>
    <a href="{{ route('inventories.index', ['alerts' => 1]) }}"
       class="sidebar-link {{ request()->is(['inventories']) && request()->boolean('alerts') ? 'active' : null }}"
       aria-current="{{ request()->is(['inventories']) && request()->boolean('alerts') ? 'page' : null }}">
      <i class="bi bi-exclamation-triangle"></i> Alertas de stock
    </a>
    <a href="{{ route('payments.index') }}"
       class="sidebar-link {{ request()->is(['payments*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['payments*']) ? 'page' : null }}">
      <i class="bi bi-cash-coin"></i> Pagos
    </a>
    <a href="{{ route('deliveries.index') }}"
       class="sidebar-link {{ request()->is(['deliveries*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['deliveries*']) ? 'page' : null }}">
      <i class="bi bi-truck"></i> Entregas
    </a>

    <p class="nav-section">Configuraciones</p>
    <a href="{{ route('roles.index') }}"
       class="sidebar-link {{ request()->is(['roles*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['roles*']) ? 'page' : null }}">
      <i class="bi bi-shield-check"></i> Roles
    </a>
    <a href="{{ route('users.index') }}"
       class="sidebar-link {{ request()->is(['users*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['users*']) ? 'page' : null }}">
      <i class="bi bi-person-gear"></i> Usuarios
    </a>
    <a href="{{ route('categories.index') }}"
       class="sidebar-link {{ request()->is(['categories*']) ? 'active' : null }}"
       aria-current="{{ request()->is(['categories*']) ? 'page' : null }}">
      <i class="bi bi-tags"></i> Categorías
    </a>
  </nav>

  <div class="sidebar-user dropup">
    <button class="user-card dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <span class="avatar soft-rosa">MQ</span>
      <span class="user-info">
        <span class="user-name d-block text-truncate">María Quispe</span>
        <span class="user-email d-block text-truncate">maria.quispe@delitex.pe</span>
      </span>
      <i class="bi bi-chevron-expand"></i>
    </button>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Mi perfil</a></li>
      <li><a class="dropdown-item" href="#"><i class="bi bi-key"></i> Cambiar contraseña</a></li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </button>
      </li>
    </ul>
  </div>
</aside>
