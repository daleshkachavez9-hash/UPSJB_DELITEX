<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | DELITEX Confecciones</title>
  <link rel="icon" href="./images/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="./css/custom.css">
</head>
<body data-page="dashboard">

  <aside class="app-sidebar" id="sidebar" aria-label="Menú principal">
    <a href="dashboard.html" class="sidebar-brand">
      <span class="brand-logo">DX</span>
      <span class="brand-name">DELITEX</span>
      <span class="brand-sub">Confecciones deportivas</span>
    </a>

    <nav class="sidebar-nav">
      <p class="nav-section">Principal</p>
      <a href="dashboard.html" class="sidebar-link active" aria-current="page"><i class="bi bi-grid-1x2"></i> Dashboard</a>
      <a href="reportes.html" class="sidebar-link"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a>

      <p class="nav-section">Gestión</p>
      <a href="clientes.html" class="sidebar-link"><i class="bi bi-people"></i> Clientes</a>
      <a href="pedidos.html" class="sidebar-link"><i class="bi bi-clipboard-check"></i> Pedidos</a>
      <a href="seguimiento.html" class="sidebar-link"><i class="bi bi-signpost-split"></i> Seguimiento de pedidos</a>
      <a href="produccion.html" class="sidebar-link"><i class="bi bi-scissors"></i> Producción</a>
      <a href="insumos.html" class="sidebar-link"><i class="bi bi-boxes"></i> Insumos</a>
      <a href="almacen.html" class="sidebar-link"><i class="bi bi-box-seam"></i> Almacén</a>
      <a href="alertas.html" class="sidebar-link"><i class="bi bi-exclamation-triangle"></i> Alertas de stock</a>
      <a href="pagos.html" class="sidebar-link"><i class="bi bi-cash-coin"></i> Pagos</a>
      <a href="entregas.html" class="sidebar-link"><i class="bi bi-truck"></i> Entregas</a>

      <p class="nav-section">Configuraciones</p>
      <a href="usuarios.html" class="sidebar-link"><i class="bi bi-person-gear"></i> Usuarios</a>
      <a href="configuracion.html" class="sidebar-link"><i class="bi bi-gear"></i> Configuración</a>
    </nav>

    <div class="sidebar-user dropup">
      <button class="user-card dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="avatar soft-rosa" id="userAvatar">MQ</span>
        <span class="user-info">
          <span class="user-name d-block text-truncate" id="userName">María Quispe</span>
          <span class="user-email d-block text-truncate" id="userEmail">maria.quispe@delitex.pe</span>
        </span>
        <i class="bi bi-chevron-expand"></i>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="configuracion.html#cuenta"><i class="bi bi-person"></i> Mi perfil</a></li>
        <li><a class="dropdown-item" href="configuracion.html#clave"><i class="bi bi-key"></i> Cambiar contraseña</a></li>
        <li><a class="dropdown-item" href="configuracion.html#general"><i class="bi bi-sliders"></i> Preferencias</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="index.html" data-cerrar-sesion><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
      </ul>
    </div>
  </aside>
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <div class="app-main">
    <header class="app-topbar">
      <button class="btn-icon" id="btnMenu" type="button" aria-label="Mostrar u ocultar menú" aria-controls="sidebar">
        <i class="bi bi-list"></i>
      </button>
      <h1 class="topbar-title">Dashboard</h1>

      <div class="ms-auto d-flex align-items-center gap-3">
        <span class="topbar-date d-none d-md-inline"><i class="bi bi-calendar3 me-1"></i><span id="fechaHoy"></span></span>

        <div class="dropdown">
          <button class="btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
            <i class="bi bi-bell"></i>
            <span class="badge rounded-pill bg-danger" id="notifBadge">0</span>
          </button>
          <div class="dropdown-menu dropdown-menu-end notif-menu">
            <h6 class="dropdown-header">Notificaciones</h6>
            <div id="notifLista"></div>
          </div>
        </div>
      </div>
    </header>

    <main class="app-content">
      <div class="page-header">
        <div>
          <h2>¡Hola, <span data-primer-nombre>María</span>!</h2>
          <p>Este es el menú principal y el resumen de pedidos, producción e inventario de hoy.</p>
        </div>
        <nav class="breadcrumb-pill" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.html"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li class="breadcrumb-item">Principal</li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
        </nav>
      </div>

      <div class="section-bar">
        <h3>Menú principal</h3>
      </div>
      <div class="tile-grid mb-4">
        <a class="module-tile" href="clientes.html">
          <span class="tile-icon soft-lavanda"><i class="bi bi-people"></i></span>
          <span class="tile-title">Clientes</span>
          <span class="tile-sub">Registrar y buscar por DNI</span>
        </a>
        <a class="module-tile" href="pedidos.html">
          <span class="tile-icon soft-azul"><i class="bi bi-clipboard-check"></i></span>
          <span class="tile-title">Pedidos</span>
          <span class="tile-sub">Registrar pedidos personalizados</span>
        </a>
        <a class="module-tile" href="seguimiento.html">
          <span class="tile-icon soft-salvia"><i class="bi bi-signpost-split"></i></span>
          <span class="tile-title">Seguimiento</span>
          <span class="tile-sub">Estado de cada pedido</span>
        </a>
        <a class="module-tile" href="produccion.html">
          <span class="tile-icon soft-amarillo"><i class="bi bi-scissors"></i></span>
          <span class="tile-title">Producción</span>
          <span class="tile-sub">Órdenes de trabajo y etapas</span>
        </a>
        <a class="module-tile" href="insumos.html">
          <span class="tile-icon soft-lavanda"><i class="bi bi-boxes"></i></span>
          <span class="tile-title">Insumos</span>
          <span class="tile-sub">Telas, hilos y accesorios</span>
        </a>
        <a class="module-tile" href="almacen.html">
          <span class="tile-icon soft-azul"><i class="bi bi-box-seam"></i></span>
          <span class="tile-title">Almacén</span>
          <span class="tile-sub">Ingresos y salidas de stock</span>
        </a>
        <a class="module-tile" href="alertas.html">
          <span class="tile-icon soft-rosa"><i class="bi bi-exclamation-triangle"></i></span>
          <span class="tile-title">Alertas de stock</span>
          <span class="tile-sub">Insumos por reponer</span>
        </a>
        <a class="module-tile" href="pagos.html">
          <span class="tile-icon soft-salvia"><i class="bi bi-cash-coin"></i></span>
          <span class="tile-title">Pagos</span>
          <span class="tile-sub">Adelantos, saldos y recibos</span>
        </a>
        <a class="module-tile" href="entregas.html">
          <span class="tile-icon soft-amarillo"><i class="bi bi-truck"></i></span>
          <span class="tile-title">Entregas</span>
          <span class="tile-sub">Salida de pedidos terminados</span>
        </a>
        <a class="module-tile" href="reportes.html">
          <span class="tile-icon soft-azul"><i class="bi bi-bar-chart-line"></i></span>
          <span class="tile-title">Reportes</span>
          <span class="tile-sub">Reportes e informes</span>
        </a>
      </div>

      <div class="section-bar">
        <h3>Resumen general</h3>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalPedido">
          <i class="bi bi-plus-lg me-1"></i> Nuevo pedido
        </button>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card stat-card">
            <div class="card-body">
              <span class="stat-icon soft-lavanda"><i class="bi bi-clipboard-check"></i></span>
              <div>
                <div class="stat-value" id="statPedidos">0</div>
                <div class="stat-label">Pedidos activos</div>
                <div class="stat-foot text-muted" id="statPedidosPie"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card stat-card">
            <div class="card-body">
              <span class="stat-icon soft-azul"><i class="bi bi-scissors"></i></span>
              <div>
                <div class="stat-value" id="statProduccion">0</div>
                <div class="stat-label">Órdenes en producción</div>
                <div class="stat-foot text-muted" id="statProduccionPie"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card stat-card">
            <div class="card-body">
              <span class="stat-icon soft-amarillo"><i class="bi bi-box-seam"></i></span>
              <div>
                <div class="stat-value" id="statStock">0</div>
                <div class="stat-label">Insumos con stock bajo</div>
                <div class="stat-foot text-danger" id="statStockPie"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card stat-card">
            <div class="card-body">
              <span class="stat-icon soft-rosa"><i class="bi bi-cash-coin"></i></span>
              <div>
                <div class="stat-value" id="statPagos">0</div>
                <div class="stat-label">Pagos pendientes</div>
                <div class="stat-foot text-muted" id="statPagosPie"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <div>
            <h3>Pedidos recientes</h3>
            <small>Últimos pedidos registrados en el taller</small>
          </div>
          <a href="pedidos.html" class="btn btn-sm btn-outline-dark">Ver todos</a>
        </div>
        <div class="card-body px-0 pb-0">
          <div class="table-responsive">
            <table class="table table-hover table-delitex">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Cliente</th>
                  <th>Prenda</th>
                  <th class="text-center">Cant.</th>
                  <th>Entrega</th>
                  <th>Estado</th>
                  <th>Pago</th>
                </tr>
              </thead>
              <tbody id="tablaPedidos"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-7">
          <div class="card h-100">
            <div class="card-header">
              <div>
                <h3>Progreso de producción</h3>
                <small>Órdenes de trabajo en el taller</small>
              </div>
              <span class="badge rounded-pill soft-azul" id="badgeProduccion">0 en curso</span>
            </div>
            <div class="card-body" id="listaProduccion"></div>
          </div>
        </div>
        <div class="col-12 col-lg-5">
          <div class="card h-100">
            <div class="card-header">
              <div>
                <h3>Alertas de inventario</h3>
                <small>Telas e insumos bajo el stock mínimo</small>
              </div>
              <a href="alertas.html" class="badge rounded-pill bg-danger text-decoration-none" id="badgeAlertas">0</a>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="list-group list-group-flush" id="listaAlertas"></div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="app-footer d-flex flex-wrap justify-content-between gap-2">
      <span>© 2026 DELITEX Confecciones · Sistema de gestión</span>
      <span>Usuario: <strong id="footerRol">Administrador</strong></span>
    </footer>
  </div>

  <div class="modal fade" id="modalPedido" tabindex="-1" aria-labelledby="modalPedidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content form-grid" id="formPedido" novalidate>
        <div class="modal-header">
          <div class="d-flex align-items-center gap-3">
            <span class="icon-chip soft-lavanda"><i class="bi bi-clipboard-plus"></i></span>
            <div>
              <h5 class="modal-title mb-0" id="modalPedidoLabel">Nuevo pedido</h5>
              <small class="text-muted">Registra las especificaciones del pedido del cliente</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-lg-8">
              <p class="form-section-title">Datos del cliente</p>
              <div class="row g-3 mb-3">
                <div class="col-md-5">
                  <label for="pDni" class="form-label">DNI del cliente</label>
                  <div class="input-group has-validation">
                    <input type="text" class="form-control" id="pDni" placeholder="8 dígitos" maxlength="8" inputmode="numeric" pattern="[0-9]{8}" required>
                    <button class="btn btn-lavanda" type="button" id="pBuscarDni"><i class="bi bi-search me-1"></i> Buscar</button>
                    <div class="invalid-feedback">Ingresa un DNI de 8 dígitos.</div>
                  </div>
                </div>
                <div class="col-md-7">
                  <span class="form-label d-block">Cliente</span>
                  <div class="info-cliente soft-azul" id="pClienteInfo"><i class="bi bi-person-vcard fs-5"></i><span>Busca al cliente por su DNI.</span></div>
                </div>
                <div class="col-12 d-none" id="pNuevoCliente">
                  <div class="row g-3">
                    <div class="col-md-5">
                      <label for="pNombre" class="form-label">Nombre del cliente nuevo</label>
                      <input type="text" class="form-control" id="pNombre" placeholder="Nombres y apellidos">
                      <div class="invalid-feedback">Ingresa el nombre del cliente.</div>
                    </div>
                    <div class="col-md-4">
                      <label for="pInstitucion" class="form-label">Institución / club</label>
                      <input type="text" class="form-control" id="pInstitucion" placeholder="Opcional">
                    </div>
                    <div class="col-md-3">
                      <label for="pTelefono" class="form-label">Teléfono</label>
                      <input type="tel" class="form-control" id="pTelefono" placeholder="9XX XXX XXX">
                    </div>
                  </div>
                </div>
              </div>

              <p class="form-section-title">Detalle de la prenda</p>
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="pPrenda" class="form-label">Tipo de prenda</label>
                  <select class="form-select" id="pPrenda" required></select>
                  <div class="invalid-feedback">Selecciona la prenda.</div>
                </div>
                <div class="col-md-4">
                  <label for="pTela" class="form-label">Tela</label>
                  <select class="form-select" id="pTela" required></select>
                  <div class="invalid-feedback">Selecciona la tela.</div>
                </div>
                <div class="col-md-4">
                  <label for="pColor" class="form-label">Color (stock en almacén)</label>
                  <select class="form-select" id="pColor" required></select>
                  <div class="invalid-feedback">Selecciona un color disponible.</div>
                </div>
                <div class="col-12">
                  <span class="form-label d-block">Cantidad por talla</span>
                  <div class="tallas-grid">
                    <label class="talla"><span>S</span><input type="number" class="form-control" id="pTallaS" min="0" value="0"></label>
                    <label class="talla"><span>M</span><input type="number" class="form-control" id="pTallaM" min="0" value="0"></label>
                    <label class="talla"><span>L</span><input type="number" class="form-control" id="pTallaL" min="0" value="0"></label>
                    <label class="talla"><span>XL</span><input type="number" class="form-control" id="pTallaXL" min="0" value="0"></label>
                    <label class="talla"><span>XXL</span><input type="number" class="form-control" id="pTallaXXL" min="0" value="0"></label>
                  </div>
                  <div class="d-flex justify-content-between mt-2 small">
                    <span class="text-danger fw-semibold d-none" id="pTallasError"><i class="bi bi-exclamation-circle me-1"></i>Ingresa al menos una prenda.</span>
                    <span class="ms-auto">Total de prendas: <strong id="pCantidad">0</strong></span>
                  </div>
                </div>
                <div class="col-md-5">
                  <label for="pEstampado" class="form-label">Estampado</label>
                  <select class="form-select" id="pEstampado"></select>
                </div>
                <div class="col-md-7">
                  <label for="pDetalle" class="form-label">Detalle del estampado</label>
                  <input type="text" class="form-control" id="pDetalle" placeholder="Ej. nombres y números de cada jugador">
                </div>
                <div class="col-12">
                  <label for="pObs" class="form-label">Observaciones</label>
                  <textarea class="form-control" id="pObs" rows="2" placeholder="Indicaciones adicionales para el taller"></textarea>
                </div>
              </div>

              <p class="form-section-title">Entrega y pago</p>
              <div class="row g-3">
                <div class="col-md-4">
                  <label for="pEntrega" class="form-label">Fecha de entrega</label>
                  <input type="date" class="form-control" id="pEntrega" required>
                  <div class="invalid-feedback">Selecciona una fecha válida.</div>
                </div>
                <div class="col-md-4">
                  <label for="pPrecio" class="form-label">Precio unitario (S/)</label>
                  <input type="number" class="form-control" id="pPrecio" min="0" step="0.01" required>
                  <div class="invalid-feedback">Ingresa el precio.</div>
                </div>
                <div class="col-md-4">
                  <label for="pTotal" class="form-label">Total del pedido</label>
                  <input type="text" class="form-control fw-bold" id="pTotal" value="S/ 0.00" readonly>
                </div>
                <div class="col-md-4">
                  <label for="pAdelanto" class="form-label">Adelanto (S/)</label>
                  <input type="number" class="form-control" id="pAdelanto" min="0" step="0.01" value="0">
                  <div class="invalid-feedback">El adelanto no puede superar el total.</div>
                </div>
                <div class="col-md-4">
                  <label for="pMetodo" class="form-label">Método de pago</label>
                  <select class="form-select" id="pMetodo"></select>
                </div>
                <div class="col-md-4">
                  <label for="pSaldo" class="form-label">Saldo pendiente</label>
                  <input type="text" class="form-control fw-bold" id="pSaldo" value="S/ 0.00" readonly>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <p class="form-section-title">Verificación de insumos</p>
              <div id="pInsumos"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Registrar pedido</button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/datos.js"></script>
  <script src="./js/app.js"></script>
</body>
</html>