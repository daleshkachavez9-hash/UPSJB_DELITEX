const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];

const PAGINAS = {
  index: iniciarLogin,
  registro: iniciarRegistro,
  recuperar: iniciarRecuperar,
  restablecer: iniciarRestablecer,
  dashboard: iniciarDashboard,
  clientes: iniciarClientes,
  pedidos: iniciarPedidos,
  seguimiento: iniciarSeguimiento,
  produccion: iniciarProduccion,
  insumos: iniciarInsumos,
  almacen: iniciarAlmacen,
  alertas: iniciarAlertas,
  pagos: iniciarPagos,
  entregas: iniciarEntregas,
  reportes: iniciarReportes,
  usuarios: iniciarUsuarios,
  configuracion: iniciarConfiguracion
};

document.addEventListener('DOMContentLoaded', () => {
  const pagina = document.body.dataset.page;
  iniciarVerClave();
  if ($('#sidebar')) iniciarPlantilla();
  if (PAGINAS[pagina]) PAGINAS[pagina]();
});


const COLOR_ESTADO = {
  'Registrado': 'lavanda',
  'En producción': 'azul',
  'Listo para entrega': 'amarillo',
  'Entregado': 'salvia',
  'Corte': 'amarillo',
  'Costura': 'azul',
  'Estampado': 'lavanda',
  'Acabado': 'salvia',
  'Normal': 'salvia',
  'Bajo': 'amarillo',
  'Crítico': 'rosa',
  'Pendiente': 'rosa',
  'Adelanto': 'lavanda',
  'Abono': 'azul',
  'Cancelación': 'salvia',
  'Pagado': 'salvia',
  'Activo': 'salvia',
  'Inactivo': 'rosa',
  'Ingreso': 'salvia',
  'Salida': 'lavanda',
  'Entrega autorizada': 'salvia',
  'Retenido por saldo': 'rosa'
};

function soles(monto) {
  return 'S/ ' + Number(monto || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fechaCorta(iso) {
  if (!iso) return '—';
  const [a, m, d] = iso.split('-');
  return `${d}/${m}/${a}`;
}

function hoyISO() {
  const h = new Date();
  return `${h.getFullYear()}-${String(h.getMonth() + 1).padStart(2, '0')}-${String(h.getDate()).padStart(2, '0')}`;
}

function esc(texto) {
  const div = document.createElement('div');
  div.textContent = texto == null ? '' : String(texto);
  return div.innerHTML;
}

function badge(estado) {
  return `<span class="status soft-${COLOR_ESTADO[estado] || 'lavanda'}">${esc(estado)}</span>`;
}

function pill(texto, color) {
  return `<span class="badge rounded-pill soft-${color}">${esc(texto)}</span>`;
}

function parametro(nombre) {
  return new URLSearchParams(location.search).get(nombre);
}

function iniciales(nombre) {
  return (nombre || '').split(' ').filter(Boolean).slice(0, 2).map((p) => p[0].toUpperCase()).join('');
}

function siguienteCodigo(prefijo, lista) {
  const n = lista.map((c) => parseInt(String(c).replace(/\D/g, ''), 10) || 0);
  return prefijo + String((n.length ? Math.max(...n) : 0) + 1).padStart(4, '0');
}

function siguienteRecibo() {
  const recibos = DB.pedidos.flatMap((p) => p.pagos.map((x) => x.recibo));
  return 'R-' + String((recibos.length ? Math.max(...recibos.map((r) => parseInt(r.slice(2), 10))) : 300) + 1).padStart(5, '0');
}

function vacio(colspan, texto, icono = 'bi-inbox') {
  return `<tr><td colspan="${colspan}"><div class="empty-state"><i class="bi ${icono}"></i>${texto}</div></td></tr>`;
}

function mostrarToast(mensaje, tipo = 'info') {
  const estilos = {
    info: { icono: 'bi-info-circle', color: 'soft-azul' },
    exito: { icono: 'bi-check-circle', color: 'soft-salvia' },
    aviso: { icono: 'bi-exclamation-triangle', color: 'soft-amarillo' },
    error: { icono: 'bi-x-circle', color: 'soft-rosa' }
  };
  const { icono, color } = estilos[tipo] || estilos.info;
  let contenedor = $('#toastContainer');
  if (!contenedor) {
    contenedor = document.createElement('div');
    contenedor.id = 'toastContainer';
    contenedor.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(contenedor);
  }
  const toast = document.createElement('div');
  toast.className = 'toast align-items-center border-0 shadow';
  toast.setAttribute('role', 'status');
  toast.setAttribute('aria-live', 'polite');
  toast.innerHTML = `
    <div class="d-flex align-items-center gap-2 p-2">
      <span class="icon-chip ${color}"><i class="bi ${icono}"></i></span>
      <div class="toast-body p-1 small fw-semibold">${mensaje}</div>
      <button type="button" class="btn-close ms-auto me-1" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>`;
  contenedor.appendChild(toast);
  toast.addEventListener('hidden.bs.toast', () => toast.remove());
  new bootstrap.Toast(toast, { delay: 4000 }).show();
}

function confirmar(mensaje, textoBoton = 'Confirmar', claseBoton = 'btn-danger') {
  return new Promise((resolver) => {
    let modal = $('#modalConfirmar');
    if (!modal) {
      modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.id = 'modalConfirmar';
      modal.tabIndex = -1;
      modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered modal-sm">
          <div class="modal-content">
            <div class="modal-body text-center pt-4">
              <span class="icon-chip soft-amarillo mx-auto mb-3" style="width:52px;height:52px;font-size:1.4rem"><i class="bi bi-question-circle"></i></span>
              <p class="mb-0 fw-semibold" id="confirmarTexto"></p>
            </div>
            <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn" id="confirmarOk"></button>
            </div>
          </div>
        </div>`;
      document.body.appendChild(modal);
    }
    const instancia = bootstrap.Modal.getOrCreateInstance(modal);
    const ok = $('#confirmarOk', modal);
    $('#confirmarTexto', modal).innerHTML = mensaje;
    ok.className = 'btn ' + claseBoton;
    ok.textContent = textoBoton;
    let aceptado = false;
    const alAceptar = () => { aceptado = true; instancia.hide(); };
    ok.addEventListener('click', alAceptar, { once: true });
    modal.addEventListener('hidden.bs.modal', () => {
      ok.removeEventListener('click', alAceptar);
      resolver(aceptado);
    }, { once: true });
    instancia.show();
  });
}

function imprimirContenido(titulo, html) {
  const ventana = window.open('', '_blank', 'width=820,height=900');
  if (!ventana) {
    mostrarToast('Permite las ventanas emergentes para imprimir.', 'aviso');
    return;
  }
  const estilos = new URL('./css/custom.css', location.href).href;
  ventana.document.write(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>${esc(titulo)}</title>
    <link rel="stylesheet" href="${estilos}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head><body class="bg-white p-4">${html}</body></html>`);
  ventana.document.close();
  ventana.addEventListener('load', () => { ventana.focus(); ventana.print(); });
}


function nombreInsumo(i) {
  return i.color ? `${i.nombre} ${i.color.toLowerCase()}` : i.nombre;
}

function buscarInsumo(codigo) {
  return DB.insumos.find((i) => i.codigo === codigo);
}

function estadoInsumo(i) {
  const critico = i.minimo * (DB.config.inventario.porcentajeCritico / 100);
  if (i.stock <= critico) return 'Crítico';
  if (i.stock < i.minimo) return 'Bajo';
  return 'Normal';
}

function insumosConAlerta() {
  return DB.insumos
    .filter((i) => estadoInsumo(i) !== 'Normal')
    .sort((a, b) => (a.stock / a.minimo) - (b.stock / b.minimo));
}

function telasDisponibles(tipo) {
  return DB.insumos.filter((i) => i.categoria === 'Tela' && i.nombre === 'Tela ' + tipo.toLowerCase());
}

function buscarCliente(dni) {
  return DB.clientes.find((c) => c.dni === dni);
}

function buscarPedido(codigo) {
  return DB.pedidos.find((p) => p.codigo === codigo.toUpperCase());
}

function clienteDe(p) {
  return buscarCliente(p.dni) || { nombre: 'Cliente no registrado', institucion: '', dni: p.dni };
}

function nombreCliente(c) {
  return c.institucion ? `${c.nombre} · ${c.institucion}` : c.nombre;
}

function pagadoDe(p) {
  return p.pagos.reduce((a, x) => a + Number(x.monto), 0);
}

function saldoDe(p) {
  return Math.max(Math.round((p.total - pagadoDe(p)) * 100) / 100, 0);
}

function estadoPago(p) {
  const pagado = pagadoDe(p);
  if (pagado <= 0) return 'Pendiente';
  if (saldoDe(p) <= 0) return 'Pagado';
  return 'Adelanto';
}

function pillPago(p) {
  const estado = estadoPago(p);
  if (estado === 'Pagado') return pill('Pagado', 'salvia');
  if (estado === 'Adelanto') return pill(`Adelanto ${Math.round((pagadoDe(p) / p.total) * 100)}%`, 'lavanda');
  return pill('Sin adelanto', 'rosa');
}

function etapasDe(p) {
  return ETAPAS.filter((e) => e !== 'Estampado' || p.estampado !== 'Sin estampado');
}

function etapaActual(p) {
  if (p.estado !== 'En producción') return null;
  const hechas = etapasDe(p).filter((e) => p.fechas[e.toLowerCase()]);
  return hechas.length ? hechas[hechas.length - 1] : etapasDe(p)[0];
}

function avanceDe(p) {
  if (p.estado === 'Registrado') return 0;
  if (p.estado !== 'En producción') return 100;
  const etapas = etapasDe(p);
  return Math.round(((etapas.indexOf(etapaActual(p)) + 1) / (etapas.length + 1)) * 100);
}

function situacionEntrega(p) {
  return saldoDe(p) <= 0 ? 'Entrega autorizada' : 'Retenido por saldo';
}

function fechaPagoCompleto(p) {
  if (saldoDe(p) > 0) return null;
  return p.pagos.length ? p.pagos[p.pagos.length - 1].fecha : null;
}

function consumoPedido(p) {
  return consumoDePedido(p, DB.config, DB.insumos);
}

function verificarStock(p) {
  return consumoPedido(p).map((r) => {
    const i = buscarInsumo(r.codigo);
    return { ...r, insumo: i, disponible: i ? i.stock : 0, alcanza: i ? i.stock >= r.cantidad : false };
  });
}

function usuarioActual() {
  const sesion = leerSesion();
  return (sesion && DB.usuarios.find((u) => u.usuario === sesion.usuario)) || DB.usuarios[0];
}


function iniciarVerClave() {
  $$('[data-ver-clave]').forEach((boton) => {
    boton.addEventListener('click', () => {
      const input = document.getElementById(boton.dataset.verClave);
      const visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      boton.innerHTML = `<i class="bi ${visible ? 'bi-eye-slash' : 'bi-eye'}"></i>`;
    });
  });
}

function iniciarPlantilla() {
  const body = document.body;
  const escritorio = window.matchMedia('(min-width: 992px)');

  const fecha = new Date().toLocaleDateString('es-PE', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  $('#fechaHoy').textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);

  $('#btnMenu').addEventListener('click', () => {
    body.classList.toggle(escritorio.matches ? 'sidebar-collapsed' : 'sidebar-open');
  });
  const cerrarMenu = () => body.classList.remove('sidebar-open');
  $('#sidebarBackdrop').addEventListener('click', cerrarMenu);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarMenu(); });
  escritorio.addEventListener('change', cerrarMenu);

  const u = usuarioActual();
  $('#userName').textContent = u.nombre;
  $('#userEmail').textContent = u.correo;
  $('#userAvatar').textContent = iniciales(u.nombre);
  $('#footerRol').textContent = u.rol;
  $$('[data-primer-nombre]').forEach((el) => { el.textContent = u.nombre.split(' ')[0]; });

  $$('[data-cerrar-sesion]').forEach((enlace) => {
    enlace.addEventListener('click', () => cerrarSesion());
  });

  pintarNotificaciones();
}

function pintarNotificaciones() {
  const items = [];
  insumosConAlerta().forEach((i) => {
    const estado = estadoInsumo(i);
    items.push({
      href: 'alertas.html',
      color: estado === 'Crítico' ? 'rosa' : 'amarillo',
      icono: 'bi-exclamation-triangle',
      titulo: `Stock ${estado.toLowerCase()}`,
      texto: `${nombreInsumo(i)}: quedan ${i.stock} ${i.unidad}.`
    });
  });
  DB.pedidos.filter((p) => p.estado === 'Listo para entrega').forEach((p) => {
    const saldo = saldoDe(p);
    items.push({
      href: saldo > 0 ? `pagos.html?pedido=${p.codigo}` : `entregas.html?pedido=${p.codigo}`,
      color: saldo > 0 ? 'amarillo' : 'salvia',
      icono: 'bi-box2-heart',
      titulo: `${p.codigo} listo para entrega`,
      texto: saldo > 0 ? `Saldo pendiente de ${soles(saldo)}.` : 'Pago completo: entrega autorizada.'
    });
  });
  const pendientesOT = DB.pedidos.filter((p) => p.estado === 'Registrado').length;
  if (pendientesOT) {
    items.push({ href: 'produccion.html', color: 'azul', icono: 'bi-scissors', titulo: 'Órdenes por generar', texto: `${pendientesOT} pedido(s) esperan su orden de trabajo.` });
  }

  $('#notifBadge').textContent = items.length;
  $('#notifBadge').classList.toggle('d-none', !items.length);
  $('#notifLista').innerHTML = items.length
    ? items.map((n) => `
      <a class="dropdown-item" href="${n.href}">
        <span class="icon-chip soft-${n.color}"><i class="bi ${n.icono}"></i></span>
        <span><strong class="d-block small">${esc(n.titulo)}</strong><span class="small text-muted">${esc(n.texto)}</span></span>
      </a>`).join('')
    : '<p class="small text-muted px-3 mb-2">No tienes notificaciones.</p>';
}


function iniciarLogin() {
  const form = $('#formLogin');
  const error = $('#loginError');
  let recordado = null;
  try { recordado = localStorage.getItem('delitex-recordar'); } catch (e) {}
  if (recordado) {
    $('#lUsuario').value = recordado;
    $('#lRecordar').checked = true;
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    error.classList.add('d-none');
    if (!form.checkValidity()) {
      error.textContent = 'Ingresa tu usuario y contraseña.';
      error.classList.remove('d-none');
      return;
    }
    const valor = $('#lUsuario').value.trim().toLowerCase();
    const u = DB.usuarios.find((x) => x.usuario.toLowerCase() === valor || x.correo.toLowerCase() === valor);
    if (!u || u.clave !== $('#lClave').value) {
      error.textContent = 'Usuario o contraseña incorrectos.';
      error.classList.remove('d-none');
      return;
    }
    if (u.estado !== 'Activo') {
      error.textContent = 'Tu usuario está inactivo. Comunícate con el administrador.';
      error.classList.remove('d-none');
      return;
    }
    try {
      if ($('#lRecordar').checked) localStorage.setItem('delitex-recordar', u.usuario);
      else localStorage.removeItem('delitex-recordar');
    } catch (err) {}
    guardarSesion(u);
    location.href = 'dashboard.html';
  });
}

function iniciarRegistro() {
  const form = $('#formRegistro');
  const confirmarClave = () => {
    $('#rConfirmar').setCustomValidity($('#rConfirmar').value !== $('#rClave').value ? 'No coincide' : '');
  };
  $('#rClave').addEventListener('input', confirmarClave);
  $('#rConfirmar').addEventListener('input', confirmarClave);

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    confirmarClave();
    const error = $('#registroError');
    error.classList.add('d-none');
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    const usuario = $('#rUsuario').value.trim();
    const correo = $('#rCorreo').value.trim().toLowerCase();
    if (DB.usuarios.some((u) => u.usuario.toLowerCase() === usuario.toLowerCase())) {
      error.textContent = 'Ese nombre de usuario ya está registrado.';
      error.classList.remove('d-none');
      return;
    }
    if (DB.usuarios.some((u) => u.correo.toLowerCase() === correo)) {
      error.textContent = 'Ese correo ya está registrado.';
      error.classList.remove('d-none');
      return;
    }
    DB.usuarios.push({ usuario, nombre: $('#rNombre').value.trim(), correo, rol: 'Recepción', estado: 'Activo', clave: $('#rClave').value });
    guardarDatos();
    form.reset();
    form.classList.remove('was-validated');
    $('#registroOk').innerHTML = '<i class="bi bi-check-circle me-1"></i> Cuenta creada correctamente. Redirigiendo al inicio de sesión…';
    $('#registroOk').classList.remove('d-none');
    setTimeout(() => { location.href = 'index.html'; }, 1800);
  });
}

function iniciarRecuperar() {
  const form = $('#formRecuperar');
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    const correo = $('#rcCorreo').value.trim().toLowerCase();
    $('#recuperarEnlace').href = 'restablecer.html?correo=' + encodeURIComponent(correo);
    $('#recuperarInfo').classList.add('d-none');
    $('#recuperarOk').classList.remove('d-none');
  });
}

function iniciarRestablecer() {
  const form = $('#formRestablecer');
  const confirmarClave = () => {
    $('#nConfirmar').setCustomValidity($('#nConfirmar').value !== $('#nClave').value ? 'No coincide' : '');
  };
  $('#nClave').addEventListener('input', confirmarClave);
  $('#nConfirmar').addEventListener('input', confirmarClave);

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    confirmarClave();
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    const correo = (parametro('correo') || '').toLowerCase();
    const u = DB.usuarios.find((x) => x.correo.toLowerCase() === correo);
    if (u) {
      u.clave = $('#nClave').value;
      guardarDatos();
    }
    form.reset();
    form.classList.remove('was-validated');
    $('#restablecerOk').classList.remove('d-none');
  });
}


function formularioPedido() {
  const f = {
    form: $('#formPedido'),
    dni: $('#pDni'),
    info: $('#pClienteInfo'),
    nuevo: $('#pNuevoCliente'),
    nombre: $('#pNombre'),
    institucion: $('#pInstitucion'),
    telefono: $('#pTelefono'),
    prenda: $('#pPrenda'),
    tela: $('#pTela'),
    color: $('#pColor'),
    estampado: $('#pEstampado'),
    detalle: $('#pDetalle'),
    obs: $('#pObs'),
    entrega: $('#pEntrega'),
    precio: $('#pPrecio'),
    total: $('#pTotal'),
    adelanto: $('#pAdelanto'),
    metodo: $('#pMetodo'),
    saldo: $('#pSaldo'),
    cantidad: $('#pCantidad'),
    tallasError: $('#pTallasError'),
    insumos: $('#pInsumos'),
    resumen: $('#pResumen')
  };
  const tallas = TALLAS.map((t) => $('#pTalla' + t));
  let clienteEncontrado = null;

  f.prenda.innerHTML = '<option value="">Selecciona…</option>' + Object.keys(DB.config.prendas).map((p) => `<option>${esc(p)}</option>`).join('');
  f.tela.innerHTML = '<option value="">Selecciona…</option>' + TIPOS_TELA.map((t) => `<option>${t}</option>`).join('');
  f.estampado.innerHTML = Object.keys(DB.config.estampados).map((e) => `<option>${esc(e)}</option>`).join('');
  f.metodo.innerHTML = METODOS_PAGO.map((m) => `<option>${m}</option>`).join('');
  f.entrega.min = hoyISO();

  const cargarColores = (seleccion) => {
    if (!f.tela.value) {
      f.color.innerHTML = '<option value="">Primero elige la tela</option>';
      return;
    }
    const telas = telasDisponibles(f.tela.value);
    f.color.innerHTML = '<option value="">Selecciona…</option>' + telas.map((i) => {
      const estado = estadoInsumo(i);
      const marca = estado === 'Normal' ? '' : ` · stock ${estado.toLowerCase()}`;
      return `<option value="${i.codigo}">${esc(i.color)} — ${i.stock} ${i.unidad}${marca}</option>`;
    }).join('');
    if (seleccion) f.color.value = seleccion;
  };

  const precioSugerido = () => {
    const cfg = DB.config.prendas[f.prenda.value];
    if (!cfg) return 0;
    return cfg.precio + (DB.config.estampados[f.estampado.value] || 0);
  };

  const cantidad = () => tallas.reduce((a, t) => a + (parseInt(t.value, 10) || 0), 0);

  const datosActuales = () => {
    const insumo = buscarInsumo(f.color.value);
    return {
      prenda: f.prenda.value,
      tela: f.tela.value,
      color: insumo ? insumo.color : '',
      telaCodigo: f.color.value,
      cantidad: cantidad(),
      estampado: f.estampado.value
    };
  };

  const pintarClienteInfo = (c, dni) => {
    if (c) {
      f.info.className = 'info-cliente soft-salvia';
      f.info.innerHTML = `<i class="bi bi-person-check fs-5"></i><span><strong>${esc(c.nombre)}</strong>${c.institucion ? ' · ' + esc(c.institucion) : ''}<br><small>${esc(c.telefono || '')}</small></span>`;
      f.nuevo.classList.add('d-none');
      f.nombre.required = false;
    } else if (dni) {
      f.info.className = 'info-cliente soft-amarillo';
      f.info.innerHTML = '<i class="bi bi-person-plus fs-5"></i><span>DNI no registrado. Completa los datos para registrar al cliente junto con el pedido.</span>';
      f.nuevo.classList.remove('d-none');
      f.nombre.required = true;
    } else {
      f.info.className = 'info-cliente soft-azul';
      f.info.innerHTML = '<i class="bi bi-person-vcard fs-5"></i><span>Busca al cliente por su DNI.</span>';
      f.nuevo.classList.add('d-none');
      f.nombre.required = false;
    }
  };

  const buscar = () => {
    const dni = f.dni.value.trim();
    if (!/^\d{8}$/.test(dni)) {
      clienteEncontrado = null;
      pintarClienteInfo(null, '');
      if (dni) mostrarToast('El DNI debe tener 8 dígitos.', 'aviso');
      return;
    }
    clienteEncontrado = buscarCliente(dni) || null;
    pintarClienteInfo(clienteEncontrado, dni);
  };

  const recalcular = () => {
    const q = cantidad();
    f.cantidad.textContent = q;
    if (q > 0) f.tallasError.classList.add('d-none');
    const precio = parseFloat(f.precio.value) || 0;
    const total = Math.round(q * precio * 100) / 100;
    const adelanto = f.adelanto.disabled ? 0 : (parseFloat(f.adelanto.value) || 0);
    f.adelanto.setCustomValidity(adelanto > total ? 'El adelanto supera el total' : '');
    f.total.value = soles(total);
    f.saldo.value = soles(Math.max(total - adelanto, 0));

    const datos = datosActuales();
    if (f.insumos) {
      if (!datos.prenda || !datos.telaCodigo || !q) {
        f.insumos.innerHTML = '<div class="info-cliente soft-azul"><i class="bi bi-box-seam fs-5"></i><span>Elige prenda, tela, color y tallas para verificar el stock.</span></div>';
      } else {
        const revision = verificarStock(datos);
        const ok = revision.every((r) => r.alcanza);
        f.insumos.innerHTML = `
          <div class="info-cliente ${ok ? 'soft-salvia' : 'soft-rosa'} mb-2">
            <i class="bi ${ok ? 'bi-check-circle' : 'bi-exclamation-octagon'} fs-5"></i>
            <span>${ok ? 'Hay stock suficiente para este pedido.' : 'Stock insuficiente: el pedido se registra, pero la orden de trabajo requerirá reposición.'}</span>
          </div>
          <ul class="resumen-lista">
            ${revision.map((r) => `<li><span>${esc(nombreInsumo(r.insumo))}</span><span class="${r.alcanza ? '' : 'text-danger'}">${r.cantidad} / ${r.disponible} ${esc(r.insumo.unidad)}</span></li>`).join('')}
          </ul>
          <small class="text-muted d-block mt-1">Requerido / disponible en almacén</small>`;
      }
    }
    if (f.resumen) {
      const c = clienteEncontrado;
      f.resumen.innerHTML = `
        <li><span>Cliente</span><span>${c ? esc(c.nombre) : (f.nombre.value ? esc(f.nombre.value) : '—')}</span></li>
        <li><span>Prenda</span><span>${esc(datos.prenda || '—')}</span></li>
        <li><span>Tela y color</span><span>${datos.tela ? esc(datos.tela + ' · ' + (datos.color || '—')) : '—'}</span></li>
        <li><span>Tallas</span><span>${TALLAS.filter((t, k) => (parseInt(tallas[k].value, 10) || 0) > 0).map((t) => `${t}:${tallas[TALLAS.indexOf(t)].value}`).join(', ') || '—'}</span></li>
        <li><span>Estampado</span><span>${esc(datos.estampado)}</span></li>
        <li><span>Entrega</span><span>${fechaCorta(f.entrega.value)}</span></li>
        <li class="total"><span>Total</span><span>${soles(total)}</span></li>`;
    }
  };

  f.dni.addEventListener('input', () => {
    f.dni.value = f.dni.value.replace(/\D/g, '').slice(0, 8);
    if (f.dni.value.length === 8) buscar();
    else { clienteEncontrado = null; pintarClienteInfo(null, ''); }
    recalcular();
  });
  $('#pBuscarDni').addEventListener('click', () => { buscar(); recalcular(); });

  f.prenda.addEventListener('change', () => {
    const cfg = DB.config.prendas[f.prenda.value];
    if (cfg) {
      f.tela.value = cfg.tela;
      cargarColores();
      f.precio.value = precioSugerido();
    }
    recalcular();
  });
  f.tela.addEventListener('change', () => { cargarColores(); recalcular(); });
  f.estampado.addEventListener('change', () => {
    f.detalle.disabled = f.estampado.value === 'Sin estampado';
    if (f.detalle.disabled) f.detalle.value = '';
    if (f.prenda.value) f.precio.value = precioSugerido();
    recalcular();
  });
  [f.color, f.precio, f.adelanto, f.entrega, f.nombre, ...tallas].forEach((el) => el.addEventListener('input', recalcular));
  f.color.addEventListener('change', recalcular);

  const limpiar = () => {
    f.form.reset();
    f.form.classList.remove('was-validated');
    tallas.forEach((t) => { t.value = 0; });
    clienteEncontrado = null;
    pintarClienteInfo(null, '');
    f.detalle.disabled = true;
    f.adelanto.disabled = false;
    f.metodo.disabled = false;
    f.dni.readOnly = false;
    f.entrega.value = sumarDias(hoyISO(), DB.config.pedidos.diasEntrega);
    cargarColores();
    f.tallasError.classList.add('d-none');
    recalcular();
  };

  const leer = () => {
    buscar();
    recalcular();
    const q = cantidad();
    f.tallasError.classList.toggle('d-none', q > 0);
    if (!f.form.checkValidity() || q === 0) {
      f.form.classList.add('was-validated');
      mostrarToast('Revisa los campos marcados del pedido.', 'aviso');
      return null;
    }
    const tallasObj = {};
    TALLAS.forEach((t, k) => { tallasObj[t] = parseInt(tallas[k].value, 10) || 0; });
    const datos = datosActuales();
    return {
      dni: f.dni.value.trim(),
      clienteNuevo: clienteEncontrado ? null : {
        dni: f.dni.value.trim(),
        nombre: f.nombre.value.trim(),
        institucion: f.institucion.value.trim(),
        telefono: f.telefono.value.trim(),
        correo: '',
        direccion: '',
        fecha: hoyISO()
      },
      prenda: datos.prenda,
      tela: datos.tela,
      color: datos.color,
      telaCodigo: datos.telaCodigo,
      tallas: tallasObj,
      cantidad: q,
      estampado: datos.estampado,
      detalleEstampado: f.detalle.value.trim(),
      observaciones: f.obs.value.trim(),
      precioUnit: parseFloat(f.precio.value) || 0,
      total: Math.round(q * (parseFloat(f.precio.value) || 0) * 100) / 100,
      fechaEntrega: f.entrega.value,
      adelanto: parseFloat(f.adelanto.value) || 0,
      metodo: f.metodo.value
    };
  };

  const cargar = (p) => {
    limpiar();
    f.dni.value = p.dni;
    buscar();
    f.prenda.value = p.prenda;
    f.tela.value = p.tela;
    cargarColores(p.telaCodigo);
    TALLAS.forEach((t, k) => { tallas[k].value = p.tallas[t] || 0; });
    f.estampado.value = p.estampado;
    f.detalle.disabled = p.estampado === 'Sin estampado';
    f.detalle.value = p.detalleEstampado || '';
    f.obs.value = p.observaciones || '';
    f.entrega.min = '';
    f.entrega.value = p.fechaEntrega;
    f.precio.value = p.precioUnit;
    f.adelanto.value = pagadoDe(p);
    f.adelanto.disabled = true;
    f.metodo.disabled = true;
    f.dni.readOnly = true;
    recalcular();
  };

  const prepararNuevo = (dni) => {
    limpiar();
    f.entrega.min = hoyISO();
    if (dni) {
      f.dni.value = dni;
      buscar();
      recalcular();
    }
  };

  limpiar();
  return { leer, cargar, limpiar: prepararNuevo, recalcular };
}

function registrarPedido(datos) {
  if (datos.clienteNuevo) DB.clientes.push(datos.clienteNuevo);
  const hoy = hoyISO();
  const p = {
    codigo: siguienteCodigo('PED-', DB.pedidos.map((x) => x.codigo)),
    dni: datos.dni,
    fecha: hoy,
    prenda: datos.prenda,
    tela: datos.tela,
    color: datos.color,
    telaCodigo: datos.telaCodigo,
    tallas: datos.tallas,
    cantidad: datos.cantidad,
    estampado: datos.estampado,
    detalleEstampado: datos.detalleEstampado,
    observaciones: datos.observaciones,
    precioUnit: datos.precioUnit,
    total: datos.total,
    fechaEntrega: datos.fechaEntrega,
    estado: 'Registrado',
    ot: null,
    fechas: { registrado: hoy },
    pagos: []
  };
  if (datos.adelanto > 0) {
    p.pagos.push({
      fecha: hoy,
      monto: datos.adelanto,
      metodo: datos.metodo,
      tipo: datos.adelanto >= datos.total ? 'Cancelación' : 'Adelanto',
      recibo: siguienteRecibo()
    });
  }
  DB.pedidos.push(p);
  guardarDatos();
  return p;
}


function iniciarDashboard() {
  const modalEl = $('#modalPedido');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  const formulario = formularioPedido();

  modalEl.addEventListener('show.bs.modal', () => formulario.limpiar());

  $('#formPedido').addEventListener('submit', (e) => {
    e.preventDefault();
    const datos = formulario.leer();
    if (!datos) return;
    const p = registrarPedido(datos);
    modal.hide();
    pintarDashboard();
    pintarNotificaciones();
    mostrarToast(`Pedido <strong>${p.codigo}</strong> registrado. <a href="seguimiento.html?pedido=${p.codigo}">Ver seguimiento</a>`, 'exito');
  });

  pintarDashboard();
}

function pintarDashboard() {
  const activos = DB.pedidos.filter((p) => p.estado !== 'Entregado');
  const enProduccion = DB.pedidos.filter((p) => p.estado === 'En producción');
  const alertas = insumosConAlerta();
  const criticos = alertas.filter((i) => estadoInsumo(i) === 'Crítico').length;
  const conSaldo = DB.pedidos.filter((p) => saldoDe(p) > 0);
  const porCobrar = conSaldo.reduce((a, p) => a + saldoDe(p), 0);
  const registrados = DB.pedidos.filter((p) => p.estado === 'Registrado').length;
  const limite = sumarDias(hoyISO(), 7);
  const entregasSemana = activos.filter((p) => p.fechaEntrega <= limite).length;

  $('#statPedidos').textContent = activos.length;
  $('#statPedidosPie').innerHTML = `<i class="bi bi-hourglass-split"></i> ${registrados} esperan orden de trabajo`;
  $('#statProduccion').textContent = enProduccion.length;
  $('#statProduccionPie').innerHTML = `<i class="bi bi-clock"></i> ${entregasSemana} entregas en 7 días`;
  $('#statStock').textContent = alertas.length;
  $('#statStockPie').innerHTML = `<i class="bi bi-exclamation-circle"></i> ${criticos} en nivel crítico`;
  $('#statPagos').textContent = conSaldo.length;
  $('#statPagosPie').innerHTML = `<i class="bi bi-wallet2"></i> ${soles(porCobrar)} por cobrar`;

  const recientes = [...DB.pedidos].sort((a, b) => b.codigo.localeCompare(a.codigo)).slice(0, 6);
  $('#tablaPedidos').innerHTML = recientes.map((p) => {
    const c = clienteDe(p);
    return `<tr class="fila-seleccionable" data-href="seguimiento.html?pedido=${p.codigo}">
      <td class="code">${p.codigo}</td>
      <td>${esc(c.institucion || c.nombre)}<br><small class="text-muted">DNI ${esc(p.dni)}</small></td>
      <td>${esc(p.prenda)}<br><small class="text-muted">${esc(p.tela)} · ${esc(p.color)}</small></td>
      <td class="text-center">${p.cantidad}</td>
      <td>${fechaCorta(p.fechaEntrega)}</td>
      <td>${badge(p.estado)}</td>
      <td>${pillPago(p)}</td>
    </tr>`;
  }).join('');
  $$('#tablaPedidos tr[data-href]').forEach((tr) => tr.addEventListener('click', () => { location.href = tr.dataset.href; }));

  $('#badgeProduccion').textContent = `${enProduccion.length} en curso`;
  $('#listaProduccion').innerHTML = enProduccion.length ? enProduccion.map((p) => {
    const c = clienteDe(p);
    const etapa = etapaActual(p);
    const etapas = etapasDe(p);
    return `<div class="prod-item">
      <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
          <span class="fw-bold">${p.ot}</span> <span class="text-muted small">· ${p.codigo}</span>
          <div class="small">${esc(c.institucion || c.nombre)} · ${p.cantidad} ${esc(p.prenda.toLowerCase())}</div>
        </div>
        ${badge(etapa)}
      </div>
      <div class="d-flex align-items-center gap-2">
        <div class="progress flex-grow-1" role="progressbar" aria-label="Avance ${p.ot}" aria-valuenow="${avanceDe(p)}" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar" style="width:${avanceDe(p)}%"></div>
        </div>
        <span class="small fw-bold">${avanceDe(p)}%</span>
      </div>
      <div class="stage-steps mt-1">${etapas.map((e) => `<span class="${p.fechas[e.toLowerCase()] ? 'done' : ''}">${e}</span>`).join('')}</div>
    </div>`;
  }).join('') : '<div class="empty-state"><i class="bi bi-scissors"></i>No hay órdenes en el taller.</div>';

  $('#badgeAlertas').textContent = alertas.length;
  $('#listaAlertas').innerHTML = alertas.length ? alertas.slice(0, 6).map((i) => {
    const estado = estadoInsumo(i);
    const color = estado === 'Crítico' ? 'rosa' : 'amarillo';
    return `<div class="list-group-item alert-item">
      <span class="icon-chip soft-${color}"><i class="bi ${estado === 'Crítico' ? 'bi-exclamation-octagon' : 'bi-exclamation-triangle'}"></i></span>
      <div class="flex-grow-1">
        <div class="alert-name">${esc(nombreInsumo(i))}</div>
        <div class="alert-meta">Stock: ${i.stock} ${esc(i.unidad)} · Mínimo: ${i.minimo} ${esc(i.unidad)}</div>
      </div>
      ${pill(estado, color)}
    </div>`;
  }).join('') : '<div class="empty-state"><i class="bi bi-check2-circle"></i>Todo el inventario está sobre el mínimo.</div>';
}


function iniciarClientes() {
  const form = $('#formCliente');
  const campos = ['Dni', 'Nombre', 'Institucion', 'Telefono', 'Correo', 'Direccion'].reduce((o, k) => { o[k.toLowerCase()] = $('#c' + k); return o; }, {});
  const modal = bootstrap.Modal.getOrCreateInstance($('#modalCliente'));
  let seleccionado = null;
  let editando = null;

  const leer = () => ({
    dni: campos.dni.value.trim(),
    nombre: campos.nombre.value.trim(),
    institucion: campos.institucion.value.trim(),
    telefono: campos.telefono.value.trim(),
    correo: campos.correo.value.trim(),
    direccion: campos.direccion.value.trim()
  });

  const rellenar = (c) => {
    editando = c ? c.dni : null;
    Object.keys(campos).forEach((k) => { campos[k].value = c ? c[k] || '' : ''; });
    campos.dni.readOnly = !!c;
    $('#cBuscar').disabled = !!c;
    $('#modalClienteLabel').textContent = c ? 'Modificar cliente' : 'Nuevo cliente';
    $('#cModo').textContent = c ? `Editando al cliente con DNI ${c.dni}` : 'Completa la información del cliente';
  };

  const abrir = (c, dniInicial = '') => {
    form.reset();
    form.classList.remove('was-validated');
    rellenar(c);
    if (!c && dniInicial) campos.dni.value = dniInicial;
    modal.show();
  };

  const pintar = () => {
    const q = $('#buscarCliente').value.trim().toLowerCase();
    const lista = DB.clientes.filter((c) => !q || [c.dni, c.nombre, c.institucion].join(' ').toLowerCase().includes(q));
    $('#cTotal').textContent = q ? `${lista.length} de ${DB.clientes.length} cliente(s)` : `${DB.clientes.length} cliente(s) registrados`;
    $('#tablaClientes').innerHTML = lista.length ? lista.map((c) => {
      const pedidos = DB.pedidos.filter((p) => p.dni === c.dni).length;
      return `<tr class="fila-seleccionable ${seleccionado === c.dni ? 'seleccionada' : ''}" data-dni="${c.dni}">
        <td class="code">${c.dni}</td>
        <td>${esc(c.nombre)}</td>
        <td>${esc(c.institucion || '—')}</td>
        <td>${esc(c.telefono || '—')}</td>
        <td class="text-center">${pedidos}</td>
        <td class="text-end text-nowrap">
          <a class="btn-accion soft-azul" href="pedidos.html?dni=${c.dni}" title="Nuevo pedido" aria-label="Nuevo pedido"><i class="bi bi-clipboard-plus"></i></a>
          <a class="btn-accion soft-salvia" href="seguimiento.html?dni=${c.dni}" title="Ver seguimiento" aria-label="Ver seguimiento"><i class="bi bi-signpost-split"></i></a>
          <button type="button" class="btn-accion soft-amarillo" data-editar="${c.dni}" title="Modificar" aria-label="Modificar ${c.dni}"><i class="bi bi-pencil-square"></i></button>
          <button type="button" class="btn-accion soft-rosa" data-eliminar="${c.dni}" title="Eliminar" aria-label="Eliminar ${c.dni}"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`;
    }).join('') : vacio(6, 'No se encontraron clientes.', 'bi-person-x');
    $$('#tablaClientes tr[data-dni]').forEach((tr) => tr.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) return;
      seleccionado = seleccionado === tr.dataset.dni ? null : tr.dataset.dni;
      pintar();
    }));
    $$('#tablaClientes [data-editar]').forEach((b) => b.addEventListener('click', () => abrir(buscarCliente(b.dataset.editar))));
    $$('#tablaClientes [data-eliminar]').forEach((b) => b.addEventListener('click', () => eliminar(buscarCliente(b.dataset.eliminar))));
  };

  const eliminar = async (c) => {
    if (DB.pedidos.some((p) => p.dni === c.dni)) {
      mostrarToast('No se puede eliminar: el cliente tiene pedidos registrados.', 'error');
      return;
    }
    if (!(await confirmar(`¿Eliminar al cliente con DNI <strong>${c.dni}</strong>?`, 'Eliminar'))) return;
    DB.clientes = DB.clientes.filter((x) => x.dni !== c.dni);
    if (seleccionado === c.dni) seleccionado = null;
    guardarDatos();
    pintar();
    mostrarToast('Cliente eliminado.', 'exito');
  };

  const requerir = () => {
    const c = seleccionado && buscarCliente(seleccionado);
    if (!c) mostrarToast('Selecciona un cliente de la tabla.', 'aviso');
    return c;
  };

  campos.dni.addEventListener('input', () => { campos.dni.value = campos.dni.value.replace(/\D/g, '').slice(0, 8); });

  $('#cBuscar').addEventListener('click', () => {
    const dni = campos.dni.value.trim();
    if (!/^\d{8}$/.test(dni)) {
      mostrarToast('Ingresa un DNI de 8 dígitos para buscar.', 'aviso');
      return;
    }
    const c = buscarCliente(dni);
    if (c) {
      rellenar(c);
      mostrarToast(`El cliente <strong>${esc(c.nombre)}</strong> ya está registrado. Puedes modificar sus datos.`, 'info');
    } else {
      mostrarToast('No existe un cliente con ese DNI. Puedes registrarlo ahora.', 'info');
      campos.nombre.focus();
    }
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    const datos = leer();
    if (editando) {
      Object.assign(buscarCliente(editando), datos);
      mostrarToast('Datos del cliente actualizados.', 'exito');
    } else {
      if (buscarCliente(datos.dni)) {
        mostrarToast('Ya existe un cliente con ese DNI.', 'error');
        return;
      }
      DB.clientes.push({ ...datos, fecha: hoyISO() });
      mostrarToast(`Cliente <strong>${esc(datos.nombre)}</strong> registrado. <a href="pedidos.html?dni=${datos.dni}">Registrar pedido</a>`, 'exito');
    }
    seleccionado = datos.dni;
    guardarDatos();
    pintar();
    modal.hide();
  });

  $$('[data-nuevo-cliente]').forEach((b) => b.addEventListener('click', () => abrir(null)));
  $('#cModificar').addEventListener('click', () => { const c = requerir(); if (c) abrir(c); });
  $('#cEliminar').addEventListener('click', () => { const c = requerir(); if (c) eliminar(c); });
  $('#buscarCliente').addEventListener('input', pintar);

  const dni = (parametro('dni') || '').replace(/\D/g, '').slice(0, 8);
  if (dni && buscarCliente(dni)) seleccionado = dni;
  pintar();
  if (dni && !buscarCliente(dni)) abrir(null, dni);
}


function iniciarPedidos() {
  const formulario = formularioPedido();
  const modal = bootstrap.Modal.getOrCreateInstance($('#modalPedido'));
  let seleccionado = null;
  let editando = null;

  const abrir = (p, opciones = {}) => {
    const soloLectura = !!p && (opciones.ver || p.estado !== 'Registrado');
    editando = p && !soloLectura ? p.codigo : null;
    if (p) {
      seleccionado = p.codigo;
      formulario.cargar(p);
    } else {
      formulario.limpiar(opciones.dni);
    }
    $('#pCampos').disabled = soloLectura;
    $('#btnGuardarPedido').classList.toggle('d-none', soloLectura);
    $('#btnGuardarPedido').innerHTML = editando ? '<i class="bi bi-save me-1"></i> Guardar cambios' : '<i class="bi bi-check2-circle me-1"></i> Registrar pedido';
    $('#pCerrar').textContent = soloLectura ? 'Cerrar' : 'Cancelar';
    $('#modalPedidoLabel').textContent = !p ? 'Nuevo pedido' : (soloLectura ? `Pedido ${p.codigo}` : `Modificar ${p.codigo}`);
    $('#pModo').textContent = !p
      ? 'Registra las especificaciones del pedido del cliente'
      : (soloLectura
        ? (p.estado === 'Registrado' ? 'Detalle del pedido (solo lectura).' : `Pedido ${p.estado.toLowerCase()}: solo lectura.`)
        : 'Puedes modificar el pedido mientras no tenga orden de trabajo.');
    pintar();
    modal.show();
  };

  const pintar = () => {
    const q = $('#buscarPedido').value.trim().toLowerCase();
    const estado = $('#filtroEstadoPedido').value;
    const lista = [...DB.pedidos]
      .sort((a, b) => b.codigo.localeCompare(a.codigo))
      .filter((p) => {
        const c = clienteDe(p);
        return (!estado || p.estado === estado) && (!q || [p.codigo, p.dni, c.nombre, c.institucion].join(' ').toLowerCase().includes(q));
      });
    $('#pTotalLista').textContent = (q || estado) ? `${lista.length} de ${DB.pedidos.length} pedido(s)` : `${DB.pedidos.length} pedido(s) registrados`;
    $('#tablaPedidosLista').innerHTML = lista.length ? lista.map((p) => {
      const c = clienteDe(p);
      return `<tr class="fila-seleccionable ${seleccionado === p.codigo ? 'seleccionada' : ''}" data-codigo="${p.codigo}">
        <td class="code">${p.codigo}</td>
        <td class="text-nowrap">${fechaCorta(p.fecha)}</td>
        <td>${p.dni}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${esc(p.prenda)}</td>
        <td class="text-center">${p.cantidad}</td>
        <td class="text-end">${soles(p.total)}</td>
        <td class="text-end">${soles(saldoDe(p))}</td>
        <td>${badge(p.estado)}</td>
        <td class="text-end text-nowrap">
          <button type="button" class="btn-accion soft-lavanda" data-ver="${p.codigo}" title="Ver detalle" aria-label="Ver ${p.codigo}"><i class="bi bi-eye"></i></button>
          <a class="btn-accion soft-salvia" href="seguimiento.html?pedido=${p.codigo}" title="Seguimiento" aria-label="Seguimiento"><i class="bi bi-signpost-split"></i></a>
          <a class="btn-accion soft-azul" href="pagos.html?pedido=${p.codigo}" title="Pagos" aria-label="Pagos"><i class="bi bi-cash-coin"></i></a>
          <button type="button" class="btn-accion soft-amarillo" data-editar="${p.codigo}" title="Modificar" aria-label="Modificar ${p.codigo}"><i class="bi bi-pencil-square"></i></button>
          <button type="button" class="btn-accion soft-rosa" data-eliminar="${p.codigo}" title="Eliminar" aria-label="Eliminar ${p.codigo}"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`;
    }).join('') : vacio(10, 'No se encontraron pedidos.');
    $$('#tablaPedidosLista tr[data-codigo]').forEach((tr) => tr.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) return;
      seleccionado = seleccionado === tr.dataset.codigo ? null : tr.dataset.codigo;
      pintar();
    }));
    $$('#tablaPedidosLista [data-ver]').forEach((b) => b.addEventListener('click', () => abrir(buscarPedido(b.dataset.ver), { ver: true })));
    $$('#tablaPedidosLista [data-editar]').forEach((b) => b.addEventListener('click', () => modificar(buscarPedido(b.dataset.editar))));
    $$('#tablaPedidosLista [data-eliminar]').forEach((b) => b.addEventListener('click', () => eliminar(buscarPedido(b.dataset.eliminar))));
  };

  const requerir = () => {
    const p = seleccionado && buscarPedido(seleccionado);
    if (!p) mostrarToast('Selecciona un pedido de la tabla.', 'aviso');
    return p;
  };

  const modificar = (p) => {
    if (p.estado !== 'Registrado') {
      mostrarToast('Solo se pueden modificar pedidos que aún no tienen orden de trabajo.', 'error');
      return;
    }
    abrir(p);
  };

  const eliminar = async (p) => {
    if (p.estado !== 'Registrado') {
      mostrarToast('No se puede eliminar un pedido que ya está en producción o entregado.', 'error');
      return;
    }
    if (!(await confirmar(`¿Eliminar el pedido <strong>${p.codigo}</strong>?${p.pagos.length ? '<br><small>Tiene pagos registrados.</small>' : ''}`, 'Eliminar'))) return;
    DB.pedidos = DB.pedidos.filter((x) => x.codigo !== p.codigo);
    if (seleccionado === p.codigo) seleccionado = null;
    guardarDatos();
    pintar();
    pintarNotificaciones();
    mostrarToast('Pedido eliminado.', 'exito');
  };

  $('#formPedido').addEventListener('submit', (e) => {
    e.preventDefault();
    const datos = formulario.leer();
    if (!datos) return;
    if (editando) {
      const p = buscarPedido(editando);
      if (datos.total < pagadoDe(p)) {
        mostrarToast('El nuevo total no puede ser menor a lo ya pagado.', 'error');
        return;
      }
      ['prenda', 'tela', 'color', 'telaCodigo', 'tallas', 'cantidad', 'estampado', 'detalleEstampado', 'observaciones', 'precioUnit', 'total', 'fechaEntrega'].forEach((k) => { p[k] = datos[k]; });
      guardarDatos();
      mostrarToast(`Pedido <strong>${p.codigo}</strong> actualizado.`, 'exito');
    } else {
      const p = registrarPedido(datos);
      seleccionado = p.codigo;
      mostrarToast(`Pedido <strong>${p.codigo}</strong> registrado. <a href="seguimiento.html?pedido=${p.codigo}">Ver seguimiento</a>`, 'exito');
    }
    modal.hide();
    pintar();
    pintarNotificaciones();
  });

  $$('[data-nuevo-pedido]').forEach((b) => b.addEventListener('click', () => { seleccionado = null; abrir(null); }));
  $('#btnVerPedido').addEventListener('click', () => { const p = requerir(); if (p) abrir(p, { ver: true }); });
  $('#btnModificarPedido').addEventListener('click', () => { const p = requerir(); if (p) modificar(p); });
  $('#btnEliminarPedido').addEventListener('click', () => { const p = requerir(); if (p) eliminar(p); });
  $('#buscarPedido').addEventListener('input', pintar);
  $('#filtroEstadoPedido').addEventListener('change', pintar);

  pintar();
  const codigo = parametro('pedido');
  const p = codigo && buscarPedido(codigo);
  const dni = (parametro('dni') || '').replace(/\D/g, '').slice(0, 8);
  if (p) abrir(p, { ver: true });
  else if (dni) abrir(null, { dni });
}


function pasosDePedido(p) {
  const primerPago = p.pagos[0];
  const pasos = [
    { titulo: 'Pedido registrado', fecha: p.fechas.registrado, icono: 'bi-clipboard-check' },
    { titulo: 'Adelanto recibido', fecha: primerPago ? primerPago.fecha : null, icono: 'bi-cash' },
    { titulo: 'Orden de trabajo', fecha: p.fechas.ot, icono: 'bi-file-earmark-text' }
  ];
  const iconos = { Corte: 'bi-scissors', Costura: 'bi-bandaid', Estampado: 'bi-palette', Acabado: 'bi-stars' };
  etapasDe(p).forEach((e) => pasos.push({ titulo: e, fecha: p.fechas[e.toLowerCase()], icono: iconos[e] }));
  pasos.push(
    { titulo: 'Listo para entrega', fecha: p.fechas.listo, icono: 'bi-box2-heart' },
    { titulo: 'Pago completo', fecha: fechaPagoCompleto(p), icono: 'bi-wallet2' },
    { titulo: 'Entregado', fecha: p.fechas.entregado, icono: 'bi-truck' }
  );
  const actual = pasos.findIndex((x) => !x.fecha);
  return pasos.map((x, k) => ({ ...x, clase: x.fecha ? 'done' : (k === actual ? 'current' : '') }));
}

function htmlFicha(p) {
  const c = clienteDe(p);
  const tallas = TALLAS.filter((t) => p.tallas[t] > 0).map((t) => `<td class="text-center">${p.tallas[t]}</td>`).join('');
  const cabecera = TALLAS.filter((t) => p.tallas[t] > 0).map((t) => `<th class="text-center">${t}</th>`).join('');
  return `
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div>
        <div class="fw-bold fs-5">${esc(DB.config.empresa.nombre)}</div>
        <div class="small text-muted">Ficha de fabricación</div>
      </div>
      <div class="text-end">
        <div class="fw-bold">${p.ot || 'Sin orden'}</div>
        <div class="small text-muted">Pedido ${p.codigo}</div>
      </div>
    </div>
    <ul class="resumen-lista mb-3">
      <li><span>Cliente</span><span>${esc(nombreCliente(c))}</span></li>
      <li><span>DNI</span><span>${esc(p.dni)}</span></li>
      <li><span>Prenda</span><span>${esc(p.prenda)}</span></li>
      <li><span>Tela y color</span><span>${esc(p.tela)} · ${esc(p.color)}</span></li>
      <li><span>Estampado</span><span>${esc(p.estampado)}</span></li>
      <li><span>Detalle del estampado</span><span>${esc(p.detalleEstampado || '—')}</span></li>
      <li><span>Observaciones</span><span>${esc(p.observaciones || '—')}</span></li>
      <li><span>Fecha de entrega</span><span>${fechaCorta(p.fechaEntrega)}</span></li>
    </ul>
    <p class="form-section-title">Distribución de tallas</p>
    <table class="table table-bordered table-sm mb-3"><thead><tr>${cabecera}<th class="text-center">Total</th></tr></thead>
      <tbody><tr>${tallas}<td class="text-center fw-bold">${p.cantidad}</td></tr></tbody></table>
    <p class="form-section-title">Insumos asignados</p>
    <ul class="resumen-lista">
      ${consumoPedido(p).map((r) => `<li><span>${esc(nombreInsumo(buscarInsumo(r.codigo)))}</span><span>${r.cantidad} ${esc(buscarInsumo(r.codigo).unidad)}</span></li>`).join('')}
    </ul>`;
}

function htmlSeguimiento(p) {
  const c = clienteDe(p);
  const pasos = pasosDePedido(p);
  const saldo = saldoDe(p);
  const porcentaje = Math.round((pasos.filter((x) => x.fecha).length / pasos.length) * 100);
  return `
    <div class="card mb-4">
      <div class="module-head">
        <span class="module-icon soft-azul"><i class="bi bi-clipboard-check"></i></span>
        <div>
          <h3>Pedido ${p.codigo}</h3>
          <p>${esc(nombreCliente(c))} · DNI ${esc(p.dni)}</p>
        </div>
        <div class="ms-auto">${badge(p.estado)}</div>
      </div>
      <div class="card-body px-4">
        <div class="stepper">
          ${pasos.map((x) => `<div class="step ${x.clase}">
            <div class="step-dot"><i class="bi ${x.fecha ? 'bi-check-lg' : x.icono}"></i></div>
            <div class="step-title">${x.titulo}</div>
            <div class="step-date">${x.fecha ? fechaCorta(x.fecha) : (x.clase === 'current' ? 'En curso' : 'Pendiente')}</div>
          </div>`).join('')}
        </div>
        <div class="d-flex align-items-center gap-2 mt-2">
          <div class="progress flex-grow-1" role="progressbar" aria-label="Avance del pedido" aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" style="width:${porcentaje}%"></div>
          </div>
          <span class="small fw-bold">${porcentaje}%</span>
        </div>
      </div>
      <div class="row g-4 px-4 pb-4 pt-2">
        <div class="col-lg-7">
          <p class="form-section-title">Especificaciones</p>
          <ul class="resumen-lista">
            <li><span>Prenda</span><span>${esc(p.prenda)}</span></li>
            <li><span>Tela y color</span><span>${esc(p.tela)} · ${esc(p.color)}</span></li>
            <li><span>Tallas</span><span>${TALLAS.filter((t) => p.tallas[t] > 0).map((t) => `${t}: ${p.tallas[t]}`).join(' · ')}</span></li>
            <li><span>Cantidad</span><span>${p.cantidad} prendas</span></li>
            <li><span>Estampado</span><span>${esc(p.estampado)}</span></li>
            <li><span>Orden de trabajo</span><span>${p.ot || 'Pendiente'}</span></li>
            <li><span>Fecha de entrega</span><span>${fechaCorta(p.fechaEntrega)}</span></li>
          </ul>
        </div>
        <div class="col-lg-5">
          <p class="form-section-title">Pagos</p>
          <ul class="resumen-lista mb-3">
            <li><span>Total</span><span>${soles(p.total)}</span></li>
            <li><span>Pagado</span><span>${soles(pagadoDe(p))}</span></li>
            <li class="total"><span>Saldo</span><span class="${saldo > 0 ? 'text-danger' : 'text-salvia'}">${soles(saldo)}</span></li>
          </ul>
          ${p.pagos.length ? `<table class="table table-sm table-delitex"><thead><tr><th>Fecha</th><th>Tipo</th><th class="text-end">Monto</th></tr></thead><tbody>
            ${p.pagos.map((x) => `<tr><td>${fechaCorta(x.fecha)}</td><td>${badge(x.tipo)}</td><td class="text-end">${soles(x.monto)}</td></tr>`).join('')}
          </tbody></table>` : '<p class="small text-muted">Aún no hay pagos registrados.</p>'}
        </div>
      </div>
      <div class="module-actions no-print">
        <button type="button" class="btn btn-primary" data-imprimir><i class="bi bi-printer me-1"></i> Imprimir seguimiento</button>
        <a href="pagos.html?pedido=${p.codigo}" class="btn btn-salvia"><i class="bi bi-cash-coin me-1"></i> Registrar pago</a>
        <a href="pedidos.html?pedido=${p.codigo}" class="btn btn-lavanda"><i class="bi bi-eye me-1"></i> Ver detalle</a>
        <a href="dashboard.html" class="btn btn-outline-dark ms-sm-auto"><i class="bi bi-arrow-left me-1"></i> Volver</a>
      </div>
    </div>`;
}

function iniciarSeguimiento() {
  const resultado = $('#sResultado');
  const inicio = resultado.innerHTML;

  const mostrar = (c, codigoActivo) => {
    const pedidos = DB.pedidos.filter((p) => p.dni === c.dni).sort((a, b) => b.codigo.localeCompare(a.codigo));
    const activo = pedidos.find((p) => p.codigo === codigoActivo) || pedidos[0];
    resultado.innerHTML = `
      <div class="card mb-4 no-print">
        <div class="card-header">
          <div>
            <h3>${esc(c.nombre)}</h3>
            <small>DNI ${esc(c.dni)}${c.institucion ? ' · ' + esc(c.institucion) : ''}${c.telefono ? ' · ' + esc(c.telefono) : ''}</small>
          </div>
          <a href="pedidos.html?dni=${c.dni}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Nuevo pedido</a>
        </div>
        <div class="card-body px-0 pb-0">
          <div class="table-responsive">
            <table class="table table-hover table-delitex">
              <thead><tr><th>Pedido</th><th>Fecha</th><th>Prenda</th><th class="text-center">Cant.</th><th>Entrega</th><th class="text-end">Saldo</th><th>Estado</th></tr></thead>
              <tbody>${pedidos.length ? pedidos.map((p) => `
                <tr class="fila-seleccionable ${activo && p.codigo === activo.codigo ? 'seleccionada' : ''}" data-codigo="${p.codigo}">
                  <td class="code">${p.codigo}</td><td class="text-nowrap">${fechaCorta(p.fecha)}</td><td>${esc(p.prenda)}</td>
                  <td class="text-center">${p.cantidad}</td><td class="text-nowrap">${fechaCorta(p.fechaEntrega)}</td>
                  <td class="text-end">${soles(saldoDe(p))}</td><td>${badge(p.estado)}</td>
                </tr>`).join('') : vacio(7, 'El cliente aún no tiene pedidos.')}</tbody>
            </table>
          </div>
        </div>
      </div>
      ${activo ? htmlSeguimiento(activo) : ''}`;
    $$('tr[data-codigo]', resultado).forEach((tr) => tr.addEventListener('click', () => mostrar(c, tr.dataset.codigo)));
    const imprimir = $('[data-imprimir]', resultado);
    if (imprimir) imprimir.addEventListener('click', () => window.print());
  };

  const buscar = (texto) => {
    const valor = texto.trim().toUpperCase();
    if (!valor) {
      mostrarToast('Ingresa un DNI o un código de pedido.', 'aviso');
      return;
    }
    if (/^\d{8}$/.test(valor)) {
      const c = buscarCliente(valor);
      if (!c) {
        resultado.innerHTML = `<div class="card"><div class="empty-state"><i class="bi bi-person-x"></i>No hay un cliente con el DNI ${esc(valor)}. <a href="clientes.html?dni=${esc(valor)}">Registrar cliente</a></div></div>`;
        return;
      }
      mostrar(c);
      return;
    }
    const codigo = /^\d+$/.test(valor) ? 'PED-' + valor.padStart(4, '0') : valor;
    const p = buscarPedido(codigo);
    if (!p) {
      resultado.innerHTML = `<div class="card"><div class="empty-state"><i class="bi bi-search"></i>No se encontró el pedido ${esc(codigo)}.</div></div>`;
      return;
    }
    mostrar(clienteDe(p), p.codigo);
  };

  $('#formSeguimiento').addEventListener('submit', (e) => {
    e.preventDefault();
    buscar($('#sBuscar').value);
  });
  $('#sLimpiar').addEventListener('click', () => {
    $('#sBuscar').value = '';
    resultado.innerHTML = inicio;
  });

  const inicial = parametro('pedido') || parametro('dni');
  if (inicial) {
    $('#sBuscar').value = inicial;
    buscar(inicial);
  }
}



function generarOrdenTrabajo(p) {
  const revision = verificarStock(p);
  const faltan = revision.filter((r) => !r.alcanza);
  if (faltan.length) {
    mostrarToast(`No se puede generar la orden de ${p.codigo}. Falta stock de: ${faltan.map((r) => `<strong>${esc(nombreInsumo(r.insumo))}</strong> (${r.cantidad} ${esc(r.insumo.unidad)} requeridos)`).join(', ')}. <a href="almacen.html">Registrar ingreso</a>`, 'error');
    return false;
  }
  const hoy = hoyISO();
  const ot = siguienteCodigo('OT-', DB.pedidos.filter((x) => x.ot).map((x) => x.ot));
  revision.forEach((r) => {
    r.insumo.stock = redondear(r.insumo.stock - r.cantidad, 2);
    DB.movimientos.push({ fecha: hoy, tipo: 'Salida', codigo: r.codigo, cantidad: r.cantidad, referencia: ot, pedido: p.codigo });
  });
  p.ot = ot;
  p.estado = 'En producción';
  p.fechas.ot = hoy;
  p.fechas.corte = hoy;
  guardarDatos();
  return true;
}

function avanzarEtapa(p) {
  const etapas = etapasDe(p);
  const idx = etapas.indexOf(etapaActual(p));
  const hoy = hoyISO();
  if (idx < etapas.length - 1) {
    p.fechas[etapas[idx + 1].toLowerCase()] = hoy;
  } else {
    p.estado = 'Listo para entrega';
    p.fechas.listo = hoy;
  }
  guardarDatos();
  return p.estado === 'Listo para entrega' ? 'Listo para entrega' : etapas[idx + 1];
}

function iniciarProduccion() {
  const modalFicha = bootstrap.Modal.getOrCreateInstance($('#modalFicha'));
  let fichaActual = null;

  const abrirFicha = (p) => {
    fichaActual = p;
    $('#fichaContenido').innerHTML = htmlFicha(p);
    modalFicha.show();
  };

  $('#btnImprimirFicha').addEventListener('click', () => {
    if (fichaActual) imprimirContenido(`Ficha ${fichaActual.ot || fichaActual.codigo}`, htmlFicha(fichaActual));
  });

  const pintar = () => {
    const pendientes = DB.pedidos.filter((p) => p.estado === 'Registrado');
    const enTaller = DB.pedidos.filter((p) => p.estado === 'En producción');
    const listos = DB.pedidos.filter((p) => p.estado === 'Listo para entrega');
    const tarjetas = [
      { etiqueta: 'Pendientes de orden', valor: pendientes.length, icono: 'bi-hourglass-split', color: 'lavanda' },
      { etiqueta: 'En taller', valor: enTaller.length, icono: 'bi-scissors', color: 'azul' },
      { etiqueta: 'Listos para entrega', valor: listos.length, icono: 'bi-box2-heart', color: 'amarillo' },
      { etiqueta: 'Prendas en confección', valor: enTaller.reduce((a, p) => a + p.cantidad, 0), icono: 'bi-handbag', color: 'salvia' }
    ];
    $('#prodResumen').innerHTML = tarjetas.map(htmlStat).join('');

    $('#tablaPendientesOT').innerHTML = pendientes.length ? pendientes.map((p) => {
      const c = clienteDe(p);
      const ok = verificarStock(p).every((r) => r.alcanza);
      return `<tr>
        <td class="code">${p.codigo}</td>
        <td class="text-nowrap">${fechaCorta(p.fecha)}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${esc(p.prenda)}<br><small class="text-muted">${esc(p.tela)} · ${esc(p.color)}</small></td>
        <td class="text-center">${p.cantidad}</td>
        <td class="text-nowrap">${fechaCorta(p.fechaEntrega)}</td>
        <td>${ok ? pill('Stock suficiente', 'salvia') : pill('Falta stock', 'rosa')}</td>
        <td class="text-end text-nowrap">
          <button type="button" class="btn-accion soft-lavanda" data-ficha="${p.codigo}" title="Ver ficha" aria-label="Ver ficha"><i class="bi bi-file-earmark-text"></i></button>
          <button type="button" class="btn btn-sm btn-primary" data-generar="${p.codigo}"><i class="bi bi-file-earmark-plus me-1"></i> Generar OT</button>
        </td>
      </tr>`;
    }).join('') : vacio(8, 'No hay pedidos pendientes de orden de trabajo.', 'bi-check2-circle');

    const filtro = $('#filtroEtapa').value;
    const ordenes = [...enTaller, ...listos]
      .filter((p) => !filtro || (filtro === 'Listo para entrega' ? p.estado === filtro : etapaActual(p) === filtro))
      .sort((a, b) => a.fechaEntrega.localeCompare(b.fechaEntrega));
    $('#tablaOT').innerHTML = ordenes.length ? ordenes.map((p) => {
      const c = clienteDe(p);
      const etapa = p.estado === 'En producción' ? etapaActual(p) : p.estado;
      const siguiente = p.estado === 'En producción'
        ? (etapasDe(p).indexOf(etapa) < etapasDe(p).length - 1 ? etapasDe(p)[etapasDe(p).indexOf(etapa) + 1] : 'Listo')
        : null;
      return `<tr>
        <td class="code">${p.ot}</td>
        <td>${p.codigo}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${esc(p.prenda)}</td>
        <td class="text-center">${p.cantidad}</td>
        <td>${badge(etapa)}</td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="progress flex-grow-1" role="progressbar" aria-label="Avance ${p.ot}" aria-valuenow="${avanceDe(p)}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width:${avanceDe(p)}%"></div></div>
            <span class="small fw-bold">${avanceDe(p)}%</span>
          </div>
        </td>
        <td class="text-nowrap">${fechaCorta(p.fechaEntrega)}</td>
        <td class="text-end text-nowrap">
          <button type="button" class="btn-accion soft-lavanda" data-ficha="${p.codigo}" title="Ficha de fabricación" aria-label="Ficha de fabricación"><i class="bi bi-file-earmark-text"></i></button>
          ${siguiente ? `<button type="button" class="btn btn-sm btn-salvia" data-avanzar="${p.codigo}" title="Pasar a ${siguiente}"><i class="bi bi-arrow-right-circle me-1"></i> ${siguiente}</button>` : `<a class="btn btn-sm btn-amarillo" href="entregas.html?pedido=${p.codigo}"><i class="bi bi-truck me-1"></i> Entrega</a>`}
        </td>
      </tr>`;
    }).join('') : vacio(9, 'No hay órdenes con ese filtro.', 'bi-scissors');

    $$('[data-ficha]').forEach((b) => b.addEventListener('click', () => abrirFicha(buscarPedido(b.dataset.ficha))));
    $$('[data-generar]').forEach((b) => b.addEventListener('click', async () => {
      const p = buscarPedido(b.dataset.generar);
      const revision = verificarStock(p);
      const detalle = revision.map((r) => `${esc(nombreInsumo(r.insumo))}: ${r.cantidad} ${esc(r.insumo.unidad)}`).join('<br>');
      if (!revision.every((r) => r.alcanza)) {
        generarOrdenTrabajo(p);
        return;
      }
      if (!(await confirmar(`¿Generar la orden de trabajo de <strong>${p.codigo}</strong>?<br><small class="text-muted">Se descontará del almacén:<br>${detalle}</small>`, 'Generar orden', 'btn-primary'))) return;
      if (generarOrdenTrabajo(p)) {
        mostrarToast(`Orden <strong>${p.ot}</strong> generada. Stock descontado automáticamente.`, 'exito');
        pintar();
        pintarNotificaciones();
        abrirFicha(p);
      }
    }));
    $$('[data-avanzar]').forEach((b) => b.addEventListener('click', () => {
      const p = buscarPedido(b.dataset.avanzar);
      const nueva = avanzarEtapa(p);
      mostrarToast(`${p.ot}: ahora en <strong>${nueva}</strong>.`, 'exito');
      pintar();
      pintarNotificaciones();
    }));
  };

  $('#filtroEtapa').addEventListener('change', pintar);
  pintar();
}

function htmlStat(t) {
  return `<div class="col-12 col-sm-6 col-xl-3">
    <div class="card stat-card"><div class="card-body">
      <span class="stat-icon soft-${t.color}"><i class="bi ${t.icono}"></i></span>
      <div><div class="stat-value">${t.valor}</div><div class="stat-label">${t.etiqueta}</div></div>
    </div></div>
  </div>`;
}


function iniciarInsumos() {
  const modalInsumo = bootstrap.Modal.getOrCreateInstance($('#modalInsumo'));
  let seleccionado = null;
  let editando = null;

  $('#filtroCategoria').innerHTML += CATEGORIAS_INSUMO.map((c) => `<option>${c}</option>`).join('');
  $('#iCategoria').innerHTML = CATEGORIAS_INSUMO.map((c) => `<option>${c}</option>`).join('');

  const pintar = () => {
    const q = $('#buscarInsumo').value.trim().toLowerCase();
    const cat = $('#filtroCategoria').value;
    const est = $('#filtroEstadoInsumo').value;
    const lista = DB.insumos.filter((i) =>
      (!cat || i.categoria === cat) &&
      (!est || estadoInsumo(i) === est) &&
      (!q || [i.codigo, i.nombre, i.color].join(' ').toLowerCase().includes(q)));
    $('#iTotal').textContent = (q || cat || est) ? `${lista.length} de ${DB.insumos.length} insumo(s)` : `${DB.insumos.length} insumo(s) registrados`;
    $('#tablaInsumos').innerHTML = lista.length ? lista.map((i) => `
      <tr class="fila-seleccionable ${seleccionado === i.codigo ? 'seleccionada' : ''}" data-codigo="${i.codigo}">
        <td class="code">${i.codigo}</td>
        <td>${esc(nombreInsumo(i))}</td>
        <td>${esc(i.categoria)}</td>
        <td class="text-center fw-bold">${i.stock} ${esc(i.unidad)}</td>
        <td class="text-center">${i.minimo} ${esc(i.unidad)}</td>
        <td>${badge(estadoInsumo(i))}</td>
        <td class="text-end text-nowrap">
          <a class="btn-accion soft-salvia" href="almacen.html?insumo=${i.codigo}" title="Ingreso de stock" aria-label="Ingreso de stock ${i.codigo}"><i class="bi bi-box-arrow-in-down"></i></a>
          <button type="button" class="btn-accion soft-amarillo" data-editar="${i.codigo}" title="Modificar" aria-label="Modificar ${i.codigo}"><i class="bi bi-pencil-square"></i></button>
          <button type="button" class="btn-accion soft-rosa" data-eliminar="${i.codigo}" title="Eliminar" aria-label="Eliminar ${i.codigo}"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`).join('') : vacio(7, 'No se encontraron insumos.');
    $$('#tablaInsumos tr[data-codigo]').forEach((tr) => tr.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) return;
      seleccionado = seleccionado === tr.dataset.codigo ? null : tr.dataset.codigo;
      pintar();
    }));
    $$('#tablaInsumos [data-editar]').forEach((b) => b.addEventListener('click', () => abrirFormulario(buscarInsumo(b.dataset.editar))));
    $$('#tablaInsumos [data-eliminar]').forEach((b) => b.addEventListener('click', () => eliminar(buscarInsumo(b.dataset.eliminar))));
  };

  const requerirSeleccion = () => {
    const i = seleccionado && buscarInsumo(seleccionado);
    if (!i) mostrarToast('Selecciona un insumo de la tabla.', 'aviso');
    return i;
  };

  const abrirFormulario = (i) => {
    editando = i ? i.codigo : null;
    const form = $('#formInsumo');
    form.reset();
    form.classList.remove('was-validated');
    $('#modalInsumoLabel').textContent = i ? `Modificar ${i.codigo}` : 'Nuevo insumo';
    $('#iModo').textContent = i ? `Editando ${nombreInsumo(i)}` : 'Completa la información del insumo';
    $('#iNombre').value = i ? i.nombre : '';
    $('#iColor').value = i ? i.color : '';
    $('#iCategoria').value = i ? i.categoria : 'Tela';
    $('#iUnidad').value = i ? i.unidad : 'm';
    $('#iStock').value = i ? i.stock : 0;
    $('#iStock').disabled = !!i;
    $('#iStockAyuda').classList.toggle('d-none', !i);
    $('#iMinimo').value = i ? i.minimo : '';
    modalInsumo.show();
  };

  const eliminar = async (i) => {
    if (DB.pedidos.some((p) => p.estado === 'Registrado' && consumoPedido(p).some((r) => r.codigo === i.codigo))) {
      mostrarToast('No se puede eliminar: hay pedidos registrados que necesitan este insumo.', 'error');
      return;
    }
    if (!(await confirmar(`¿Eliminar <strong>${esc(nombreInsumo(i))}</strong> del inventario?`, 'Eliminar'))) return;
    DB.insumos = DB.insumos.filter((x) => x.codigo !== i.codigo);
    if (seleccionado === i.codigo) seleccionado = null;
    guardarDatos();
    pintar();
    pintarNotificaciones();
    mostrarToast('Insumo eliminado.', 'exito');
  };

  $$('[data-nuevo-insumo]').forEach((b) => b.addEventListener('click', () => abrirFormulario(null)));
  $('#btnModificarInsumo').addEventListener('click', () => { const i = requerirSeleccion(); if (i) abrirFormulario(i); });
  $('#btnEliminarInsumo').addEventListener('click', () => { const i = requerirSeleccion(); if (i) eliminar(i); });
  $('#btnIngresoInsumo').addEventListener('click', () => {
    const i = requerirSeleccion();
    if (i) location.href = `almacen.html?insumo=${i.codigo}`;
  });

  $('#formInsumo').addEventListener('submit', (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    const datos = {
      nombre: $('#iNombre').value.trim(),
      color: $('#iColor').value.trim(),
      categoria: $('#iCategoria').value,
      unidad: $('#iUnidad').value,
      minimo: parseFloat($('#iMinimo').value) || 0
    };
    if (editando) {
      Object.assign(buscarInsumo(editando), datos);
      mostrarToast('Insumo actualizado.', 'exito');
    } else {
      const codigo = 'INS-' + String(Math.max(...DB.insumos.map((i) => parseInt(i.codigo.slice(4), 10)), 0) + 1).padStart(3, '0');
      const stock = parseFloat($('#iStock').value) || 0;
      DB.insumos.push({ codigo, ...datos, stock });
      if (stock > 0) DB.movimientos.push({ fecha: hoyISO(), tipo: 'Ingreso', codigo, cantidad: stock, referencia: 'Stock inicial', pedido: '' });
      seleccionado = codigo;
      mostrarToast(`Insumo <strong>${codigo}</strong> registrado.`, 'exito');
    }
    guardarDatos();
    modalInsumo.hide();
    pintar();
    pintarNotificaciones();
  });

  $('#buscarInsumo').addEventListener('input', pintar);
  $('#filtroCategoria').addEventListener('change', pintar);
  $('#filtroEstadoInsumo').addEventListener('change', pintar);

  const codigo = parametro('insumo');
  if (codigo && buscarInsumo(codigo)) seleccionado = codigo;
  pintar();
}


function iniciarAlmacen() {
  const modal = bootstrap.Modal.getOrCreateInstance($('#modalMovimiento'));
  const form = $('#formMovimiento');
  const tipoActual = () => $('input[name="mTipo"]:checked').value;

  $('#filtroCategoriaMovimiento').innerHTML += CATEGORIAS_INSUMO.map((c) => `<option>${c}</option>`).join('');

  const pintarResumen = () => {
    const mes = hoyISO().slice(0, 7);
    const delMes = DB.movimientos.filter((m) => m.fecha.startsWith(mes));
    $('#almacenResumen').innerHTML = [
      { etiqueta: 'Insumos en almacén', valor: DB.insumos.length, icono: 'bi-boxes', color: 'lavanda' },
      { etiqueta: 'Ingresos del mes', valor: delMes.filter((m) => m.tipo === 'Ingreso').length, icono: 'bi-box-arrow-in-down', color: 'salvia' },
      { etiqueta: 'Salidas del mes', valor: delMes.filter((m) => m.tipo === 'Salida').length, icono: 'bi-box-arrow-up', color: 'azul' },
      { etiqueta: 'Insumos con alerta', valor: insumosConAlerta().length, icono: 'bi-exclamation-triangle', color: 'rosa' }
    ].map(htmlStat).join('');
  };

  const pintar = () => {
    const q = $('#buscarMovimiento').value.trim().toLowerCase();
    const tipo = $('#filtroTipoMovimiento').value;
    const cat = $('#filtroCategoriaMovimiento').value;
    const lista = DB.movimientos
      .map((m, k) => ({ m, k, i: buscarInsumo(m.codigo) }))
      .filter(({ m, i }) =>
        (!tipo || m.tipo === tipo) &&
        (!cat || (i && i.categoria === cat)) &&
        (!q || [m.codigo, i ? nombreInsumo(i) : '', m.referencia, m.pedido].join(' ').toLowerCase().includes(q)))
      .sort((a, b) => b.m.fecha.localeCompare(a.m.fecha) || b.k - a.k);
    $('#mTotal').textContent = (q || tipo || cat) ? `${lista.length} de ${DB.movimientos.length} movimiento(s)` : `${DB.movimientos.length} movimiento(s) registrados`;
    $('#tablaMovimientos').innerHTML = lista.length ? lista.map(({ m, i }) => `<tr>
        <td class="text-nowrap">${fechaCorta(m.fecha)}</td>
        <td>${badge(m.tipo)}</td>
        <td class="code">${esc(m.codigo)}</td>
        <td>${esc(i ? nombreInsumo(i) : 'Insumo eliminado')}</td>
        <td class="text-center fw-bold ${m.tipo === 'Salida' ? 'text-danger' : 'text-salvia'}">${m.tipo === 'Salida' ? '−' : '+'}${m.cantidad} ${esc(i ? i.unidad : '')}</td>
        <td>${esc(m.referencia)}${m.pedido ? ` · <a href="seguimiento.html?pedido=${m.pedido}">${m.pedido}</a>` : ''}</td>
        <td class="text-center">${i ? `${i.stock} ${esc(i.unidad)}` : '—'}</td>
      </tr>`).join('') : vacio(7, 'No se encontraron movimientos.', 'bi-arrow-left-right');
    pintarResumen();
  };

  const pintarStock = () => {
    const i = buscarInsumo($('#mInsumo').value);
    const info = $('#mStockInfo');
    const cantidad = $('#mCantidad');
    $('#mUnidad').textContent = i ? i.unidad : '—';
    if (!i) {
      info.className = 'info-cliente soft-azul';
      info.innerHTML = '<i class="bi bi-box-seam fs-5"></i><span>Selecciona un insumo para ver su stock.</span>';
      cantidad.removeAttribute('max');
      return;
    }
    const estado = estadoInsumo(i);
    const color = estado === 'Normal' ? 'salvia' : (estado === 'Crítico' ? 'rosa' : 'amarillo');
    info.className = `info-cliente soft-${color}`;
    info.innerHTML = `<i class="bi bi-box-seam fs-5"></i><span>Stock actual: <strong>${i.stock} ${esc(i.unidad)}</strong> · Mínimo: ${i.minimo} ${esc(i.unidad)} · ${esc(estado)}</span>`;
    if (tipoActual() === 'Salida') cantidad.max = i.stock;
    else cantidad.removeAttribute('max');
    $('#mCantidadError').textContent = tipoActual() === 'Salida' ? `Ingresa una cantidad entre 0.1 y ${i.stock} ${i.unidad}.` : 'Ingresa una cantidad mayor a cero.';
  };

  const aplicarTipo = () => {
    const salida = tipoActual() === 'Salida';
    $('#modalMovimientoLabel').textContent = salida ? 'Registrar salida' : 'Registrar ingreso';
    $('#mIcono').className = `icon-chip ${salida ? 'soft-lavanda' : 'soft-salvia'}`;
    $('#mIcono').innerHTML = `<i class="bi ${salida ? 'bi-box-arrow-up' : 'bi-box-arrow-in-down'}"></i>`;
    $('#mPedidoGrupo').classList.toggle('d-none', !salida);
    const ref = $('#mReferencia');
    if (!ref.dataset.editado) ref.value = salida ? 'Salida a taller' : 'Compra a proveedor';
    pintarStock();
  };

  const abrir = (tipo = 'Ingreso', codigo = '') => {
    form.reset();
    form.classList.remove('was-validated');
    $('#mInsumo').innerHTML = '<option value="">Selecciona…</option>' + CATEGORIAS_INSUMO.map((c) => {
      const items = DB.insumos.filter((i) => i.categoria === c);
      return items.length ? `<optgroup label="${c}">${items.map((i) => `<option value="${i.codigo}">${i.codigo} · ${esc(nombreInsumo(i))} (${i.stock} ${esc(i.unidad)})</option>`).join('')}</optgroup>` : '';
    }).join('');
    $('#mPedido').innerHTML = '<option value="">Sin pedido</option>' + DB.pedidos
      .filter((p) => p.estado !== 'Entregado')
      .sort((a, b) => b.codigo.localeCompare(a.codigo))
      .map((p) => `<option>${p.codigo}</option>`).join('');
    $('#mInsumo').value = codigo;
    $('#mFecha').value = hoyISO();
    $('#mFecha').max = hoyISO();
    delete $('#mReferencia').dataset.editado;
    $(tipo === 'Salida' ? '#mTipoSalida' : '#mTipoIngreso').checked = true;
    aplicarTipo();
    modal.show();
  };

  $$('input[name="mTipo"]').forEach((r) => r.addEventListener('change', aplicarTipo));
  $('#mInsumo').addEventListener('change', pintarStock);
  $('#mReferencia').addEventListener('input', (e) => { e.target.dataset.editado = '1'; });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const i = buscarInsumo($('#mInsumo').value);
    const cantidad = Math.round((parseFloat($('#mCantidad').value) || 0) * 100) / 100;
    const salida = tipoActual() === 'Salida';
    $('#mCantidad').setCustomValidity(salida && i && cantidad > i.stock ? 'Supera el stock' : '');
    if (!form.checkValidity() || !i || cantidad <= 0) {
      form.classList.add('was-validated');
      if (salida && i && cantidad > i.stock) mostrarToast(`Stock insuficiente: solo hay ${i.stock} ${esc(i.unidad)}.`, 'error');
      return;
    }
    i.stock = redondear(i.stock + (salida ? -cantidad : cantidad), 2);
    DB.movimientos.push({
      fecha: $('#mFecha').value,
      tipo: salida ? 'Salida' : 'Ingreso',
      codigo: i.codigo,
      cantidad,
      referencia: $('#mReferencia').value.trim() || (salida ? 'Salida' : 'Ingreso'),
      pedido: salida ? $('#mPedido').value : ''
    });
    guardarDatos();
    modal.hide();
    pintar();
    pintarNotificaciones();
    mostrarToast(`${salida ? 'Salida registrada' : 'Ingreso registrado'}:${esc(nombreInsumo(i))} ahora tiene ${i.stock} ${esc(i.unidad)}.`, 'exito');
  });

  $$('[data-nuevo-movimiento]').forEach((b) => b.addEventListener('click', () => abrir(b.dataset.nuevoMovimiento || 'Ingreso')));
  $('#buscarMovimiento').addEventListener('input', pintar);
  $('#filtroTipoMovimiento').addEventListener('change', pintar);
  $('#filtroCategoriaMovimiento').addEventListener('change', pintar);

  pintar();
  const codigo = parametro('insumo');
  if (codigo && buscarInsumo(codigo)) abrir('Ingreso', codigo);
}


function iniciarAlertas() {
  const pintar = () => {
    const alertas = insumosConAlerta();
    const criticos = alertas.filter((i) => estadoInsumo(i) === 'Crítico');
    const afectados = (i) => DB.pedidos.filter((p) => p.estado === 'Registrado' && consumoPedido(p).some((r) => r.codigo === i.codigo));
    const pedidosAfectados = new Set(alertas.flatMap((i) => afectados(i).map((p) => p.codigo)));
    $('#alertasResumen').innerHTML = [
      { etiqueta: 'Insumos con alerta', valor: alertas.length, icono: 'bi-bell', color: 'lavanda' },
      { etiqueta: 'Nivel crítico', valor: criticos.length, icono: 'bi-exclamation-octagon', color: 'rosa' },
      { etiqueta: 'Stock bajo', valor: alertas.length - criticos.length, icono: 'bi-exclamation-triangle', color: 'amarillo' },
      { etiqueta: 'Pedidos afectados', valor: pedidosAfectados.size, icono: 'bi-clipboard-x', color: 'azul' }
    ].map(htmlStat).join('');

    $('#tablaAlertas').innerHTML = alertas.length ? alertas.map((i) => {
      const n = afectados(i);
      return `<tr>
        <td class="code">${i.codigo}</td>
        <td>${esc(nombreInsumo(i))}</td>
        <td>${esc(i.categoria)}</td>
        <td class="text-center fw-bold">${i.stock} ${esc(i.unidad)}</td>
        <td class="text-center">${i.minimo} ${esc(i.unidad)}</td>
        <td class="text-center text-danger fw-bold">${redondear(i.minimo - i.stock, 2)} ${esc(i.unidad)}</td>
        <td class="text-center">${n.length ? n.map((p) => `<a href="pedidos.html?pedido=${p.codigo}">${p.codigo}</a>`).join(', ') : '—'}</td>
        <td>${badge(estadoInsumo(i))}</td>
        <td class="text-end"><a class="btn btn-sm btn-salvia text-nowrap" href="almacen.html?insumo=${i.codigo}"><i class="bi bi-box-arrow-in-down me-1"></i> Registrar ingreso</a></td>
      </tr>`;
    }).join('') : vacio(9, 'No hay alertas: todo el inventario está sobre el stock mínimo.', 'bi-check2-circle');
  };

  $('#btnActualizarAlertas').addEventListener('click', () => {
    pintar();
    pintarNotificaciones();
    mostrarToast('Alertas actualizadas.', 'info');
  });
  pintar();
}


function htmlRecibo(p, pago) {
  const c = clienteDe(p);
  const e = DB.config.empresa;
  return `
    <div class="text-center mb-3">
      <div class="fw-bold fs-5">${esc(e.nombre)}</div>
      <div class="small text-muted">RUC ${esc(e.ruc)} · ${esc(e.direccion)}</div>
      <div class="small text-muted">${esc(e.telefono)} · ${esc(e.correo)}</div>
    </div>
    <div class="d-flex justify-content-between mb-3">
      <span class="fw-bold">Recibo interno ${esc(pago.recibo)}</span>
      <span>${fechaCorta(pago.fecha)}</span>
    </div>
    <ul class="resumen-lista">
      <li><span>Cliente</span><span>${esc(c.nombre)}</span></li>
      <li><span>DNI</span><span>${esc(p.dni)}</span></li>
      <li><span>Pedido</span><span>${p.codigo} · ${esc(p.prenda)} (${p.cantidad})</span></li>
      <li><span>Concepto</span><span>${esc(pago.tipo)}</span></li>
      <li><span>Método</span><span>${esc(pago.metodo)}</span></li>
      <li class="total"><span>Monto recibido</span><span>${soles(pago.monto)}</span></li>
      <li><span>Total del pedido</span><span>${soles(p.total)}</span></li>
      <li><span>Saldo después del pago</span><span>${soles(Math.max(p.total - p.pagos.slice(0, p.pagos.indexOf(pago) + 1).reduce((a, x) => a + x.monto, 0), 0))}</span></li>
    </ul>`;
}

function coincidePedido(p, q) {
  if (!q) return true;
  const c = clienteDe(p);
  return [p.codigo, p.dni, c.nombre, c.institucion].join(' ').toLowerCase().includes(q.toLowerCase());
}


function iniciarPagos() {
  const modalPago = bootstrap.Modal.getOrCreateInstance($('#modalPago'));
  const modalRecibo = bootstrap.Modal.getOrCreateInstance($('#modalRecibo'));
  const form = $('#formPago');
  let seleccionado = null;
  let reciboActual = null;

  $('#pgFiltroMetodo').innerHTML += METODOS_PAGO.map((m) => `<option>${m}</option>`).join('');
  $('#mpMetodo').innerHTML = METODOS_PAGO.map((m) => `<option>${m}</option>`).join('');

  const conSaldo = () => DB.pedidos.filter((p) => saldoDe(p) > 0).sort((a, b) => b.codigo.localeCompare(a.codigo));

  const abrirRecibo = (p, pago) => {
    reciboActual = { p, pago };
    $('#reciboContenido').innerHTML = htmlRecibo(p, pago);
    modalRecibo.show();
  };

  $('#btnImprimirRecibo').addEventListener('click', () => {
    if (reciboActual) imprimirContenido(`Recibo ${reciboActual.pago.recibo}`, htmlRecibo(reciboActual.p, reciboActual.pago));
  });

  const pintarResumen = () => {
    const mes = hoyISO().slice(0, 7);
    const pagosMes = DB.pedidos.flatMap((p) => p.pagos).filter((x) => x.fecha.startsWith(mes));
    const pendientes = conSaldo();
    $('#pagosResumen').innerHTML = [
      { etiqueta: 'Cobrado este mes', valor: soles(pagosMes.reduce((a, x) => a + x.monto, 0)), icono: 'bi-cash-stack', color: 'salvia' },
      { etiqueta: 'Pagos del mes', valor: pagosMes.length, icono: 'bi-receipt', color: 'lavanda' },
      { etiqueta: 'Pedidos con saldo', valor: pendientes.length, icono: 'bi-hourglass-split', color: 'amarillo' },
      { etiqueta: 'Por cobrar', valor: soles(pendientes.reduce((a, p) => a + saldoDe(p), 0)), icono: 'bi-wallet2', color: 'rosa' }
    ].map(htmlStat).join('');
  };

  const pintarSaldos = () => {
    const q = $('#pgBuscar').value.trim();
    const todos = conSaldo();
    const lista = todos.filter((p) => coincidePedido(p, q));
    $('#pgTotalSaldo').textContent = q ? `${lista.length} de ${todos.length} pedido(s)` : `${todos.length} pedido(s) con saldo pendiente`;
    $('#tablaSaldos').innerHTML = lista.length ? lista.map((p) => {
      const c = clienteDe(p);
      return `<tr class="fila-seleccionable ${seleccionado === p.codigo ? 'seleccionada' : ''}" data-codigo="${p.codigo}">
        <td class="code">${p.codigo}</td>
        <td>${p.dni}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${badge(p.estado)}</td>
        <td class="text-end">${soles(p.total)}</td>
        <td class="text-end">${soles(pagadoDe(p))}</td>
        <td class="text-end fw-bold text-danger">${soles(saldoDe(p))}</td>
        <td class="text-end text-nowrap">
          <button type="button" class="btn btn-sm btn-salvia" data-pagar="${p.codigo}"><i class="bi bi-cash-coin me-1"></i> Pagar</button>
          <a class="btn-accion soft-lavanda" href="seguimiento.html?pedido=${p.codigo}" title="Seguimiento" aria-label="Seguimiento ${p.codigo}"><i class="bi bi-signpost-split"></i></a>
        </td>
      </tr>`;
    }).join('') : vacio(8, q ? 'No hay pedidos con saldo que coincidan.' : 'No hay saldos pendientes: todos los pedidos están pagados.', 'bi-check2-circle');
    $$('#tablaSaldos tr[data-codigo]').forEach((tr) => tr.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) return;
      seleccionado = seleccionado === tr.dataset.codigo ? null : tr.dataset.codigo;
      pintarSaldos();
    }));
    $$('#tablaSaldos [data-pagar]').forEach((b) => b.addEventListener('click', () => abrir(b.dataset.pagar)));
  };

  const pintarHistorial = () => {
    const q = $('#pgBuscarHistorial').value.trim().toLowerCase();
    const metodo = $('#pgFiltroMetodo').value;
    const todos = DB.pedidos.flatMap((p) => p.pagos.map((pago, k) => ({ p, pago, k })));
    const lista = todos
      .filter(({ p, pago }) => (!metodo || pago.metodo === metodo) && (!q || pago.recibo.toLowerCase().includes(q) || coincidePedido(p, q)))
      .sort((a, b) => b.pago.fecha.localeCompare(a.pago.fecha) || b.pago.recibo.localeCompare(a.pago.recibo));
    $('#pgTotalHistorial').textContent = `${lista.length} pago(s) · ${soles(lista.reduce((a, x) => a + x.pago.monto, 0))}`;
    $('#tablaHistorialPagos').innerHTML = lista.length ? lista.map(({ p, pago, k }) => {
      const c = clienteDe(p);
      return `<tr>
        <td class="text-nowrap">${fechaCorta(pago.fecha)}</td>
        <td class="code">${esc(pago.recibo)}</td>
        <td>${p.codigo}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${badge(pago.tipo)}</td>
        <td>${esc(pago.metodo)}</td>
        <td class="text-end fw-bold">${soles(pago.monto)}</td>
        <td class="text-end"><button type="button" class="btn-accion soft-lavanda" data-recibo="${p.codigo}|${k}" title="Ver recibo" aria-label="Ver recibo ${esc(pago.recibo)}"><i class="bi bi-receipt"></i></button></td>
      </tr>`;
    }).join('') : vacio(8, 'No se encontraron pagos.', 'bi-receipt');
    $$('#tablaHistorialPagos [data-recibo]').forEach((b) => b.addEventListener('click', () => {
      const [codigo, k] = b.dataset.recibo.split('|');
      const p = buscarPedido(codigo);
      abrirRecibo(p, p.pagos[Number(k)]);
    }));
  };

  const pintar = () => {
    pintarResumen();
    pintarSaldos();
    pintarHistorial();
  };

  const pintarOpciones = (preferido) => {
    const q = $('#mpBuscar').value.trim();
    const lista = conSaldo().filter((p) => coincidePedido(p, q));
    const select = $('#mpPedido');
    select.innerHTML = lista.length
      ? (lista.length > 1 ? '<option value="">Selecciona…</option>' : '') + lista.map((p) => `<option value="${p.codigo}">${p.codigo} · ${esc(clienteDe(p).institucion || clienteDe(p).nombre)} · saldo ${soles(saldoDe(p))}</option>`).join('')
      : '<option value="">Sin pedidos con saldo</option>';
    if (preferido && lista.some((p) => p.codigo === preferido)) select.value = preferido;
    pintarDetallePago();
  };

  const pintarDetallePago = () => {
    const p = $('#mpPedido').value && buscarPedido($('#mpPedido').value);
    const monto = $('#mpMonto');
    if (!p) {
      $('#mpDetalle').innerHTML = '<div class="info-cliente soft-azul"><i class="bi bi-search fs-5"></i><span>Busca y selecciona el pedido que se va a pagar.</span></div>';
      monto.value = '';
      monto.removeAttribute('max');
      $('#mpTipo').value = '—';
      return;
    }
    const c = clienteDe(p);
    const saldo = saldoDe(p);
    $('#mpDetalle').innerHTML = `
      <div class="info-cliente soft-salvia mb-2"><i class="bi bi-person-check fs-5"></i><span><strong>${esc(c.nombre)}</strong> · DNI ${esc(p.dni)}${c.institucion ? ' · ' + esc(c.institucion) : ''}<br><small>${esc(p.prenda)} · ${p.cantidad} prendas · ${esc(p.estado)}</small></span></div>
      <ul class="resumen-lista">
        <li><span>Total del pedido</span><span>${soles(p.total)}</span></li>
        <li><span>Pagado</span><span>${soles(pagadoDe(p))}</span></li>
        <li class="total"><span>Saldo pendiente</span><span class="text-danger">${soles(saldo)}</span></li>
      </ul>`;
    monto.max = saldo;
    monto.value = saldo;
    $('#mpMontoError').textContent = `Ingresa un monto entre S/ 0.01 y ${soles(saldo)}.`;
    actualizarTipo();
  };

  const actualizarTipo = () => {
    const p = $('#mpPedido').value && buscarPedido($('#mpPedido').value);
    if (!p) return;
    const monto = parseFloat($('#mpMonto').value) || 0;
    $('#mpTipo').value = monto >= saldoDe(p) ? 'Cancelación' : (p.pagos.length ? 'Abono' : 'Adelanto');
  };

  const abrir = (codigo = '', busqueda = '') => {
    form.reset();
    form.classList.remove('was-validated');
    $('#mpBuscar').value = busqueda || codigo;
    pintarOpciones(codigo);
    modalPago.show();
  };

  $('#mpBuscar').addEventListener('input', () => pintarOpciones($('#mpPedido').value));
  $('#mpPedido').addEventListener('change', pintarDetallePago);
  $('#mpMonto').addEventListener('input', actualizarTipo);

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const p = $('#mpPedido').value && buscarPedido($('#mpPedido').value);
    const monto = Math.round((parseFloat($('#mpMonto').value) || 0) * 100) / 100;
    if (!form.checkValidity() || !p || monto <= 0 || monto > saldoDe(p)) {
      form.classList.add('was-validated');
      return;
    }
    const saldo = saldoDe(p);
    const tipo = monto >= saldo ? 'Cancelación' : (p.pagos.length ? 'Abono' : 'Adelanto');
    const pago = { fecha: hoyISO(), monto, metodo: $('#mpMetodo').value, tipo, recibo: siguienteRecibo() };
    p.pagos.push(pago);
    guardarDatos();
    seleccionado = saldoDe(p) > 0 ? p.codigo : null;
    modalPago.hide();
    pintar();
    pintarNotificaciones();
    const aviso = p.estado === 'Listo para entrega' && saldoDe(p) <= 0 ? ` <a href="entregas.html?pedido=${p.codigo}">Autorizar entrega</a>` : '';
    mostrarToast(`Pago de ${soles(monto)} registrado (${esc(pago.recibo)}).${aviso}`, 'exito');
    $('#modalPago').addEventListener('hidden.bs.modal', () => abrirRecibo(p, pago), { once: true });
  });

  $$('[data-nuevo-pago]').forEach((b) => b.addEventListener('click', () => abrir(seleccionado || '')));
  $('#pgBuscar').addEventListener('input', pintarSaldos);
  $('#pgBuscarHistorial').addEventListener('input', pintarHistorial);
  $('#pgFiltroMetodo').addEventListener('change', pintarHistorial);

  pintar();
  const codigo = parametro('pedido');
  const dni = parametro('dni');
  const p = codigo && buscarPedido(codigo);
  if (p && saldoDe(p) > 0) {
    seleccionado = p.codigo;
    pintarSaldos();
    abrir(p.codigo);
  } else if (p) {
    $('#pgBuscarHistorial').value = p.codigo;
    pintarHistorial();
    mostrarToast(`El pedido <strong>${p.codigo}</strong> no tiene saldo pendiente.`, 'info');
  } else if (dni) {
    abrir('', dni.replace(/\D/g, '').slice(0, 8));
  }
}


function iniciarEntregas() {
  const modal = bootstrap.Modal.getOrCreateInstance($('#modalEntrega'));
  const form = $('#formEntrega');
  let seleccionado = null;

  const listos = () => DB.pedidos.filter((p) => p.estado === 'Listo para entrega').sort((a, b) => (a.fechas.listo || '').localeCompare(b.fechas.listo || ''));

  const pintarResumen = () => {
    const mes = hoyISO().slice(0, 7);
    const l = listos();
    const autorizados = l.filter((p) => saldoDe(p) <= 0).length;
    $('#entregasResumen').innerHTML = [
      { etiqueta: 'Listos para entrega', valor: l.length, icono: 'bi-box2-heart', color: 'amarillo' },
      { etiqueta: 'Entrega autorizada', valor: autorizados, icono: 'bi-shield-check', color: 'salvia' },
      { etiqueta: 'Retenidos por saldo', valor: l.length - autorizados, icono: 'bi-shield-exclamation', color: 'rosa' },
      { etiqueta: 'Entregados este mes', valor: DB.pedidos.filter((p) => p.estado === 'Entregado' && (p.fechas.entregado || '').startsWith(mes)).length, icono: 'bi-truck', color: 'lavanda' }
    ].map(htmlStat).join('');
  };

  const pintarListos = () => {
    const q = $('#enBuscar').value.trim();
    const todos = listos();
    const lista = todos.filter((p) => coincidePedido(p, q));
    $('#enTotalListos').textContent = q ? `${lista.length} de ${todos.length} pedido(s)` : `${todos.length} pedido(s) esperando entrega`;
    $('#tablaListos').innerHTML = lista.length ? lista.map((p) => {
      const c = clienteDe(p);
      const saldo = saldoDe(p);
      return `<tr class="fila-seleccionable ${seleccionado === p.codigo ? 'seleccionada' : ''}" data-codigo="${p.codigo}">
        <td class="code">${p.codigo}</td>
        <td>${p.dni}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${esc(p.prenda)} <small class="text-muted">(${p.cantidad})</small></td>
        <td class="text-nowrap">${fechaCorta(p.fechas.listo)}</td>
        <td class="text-end fw-bold ${saldo > 0 ? 'text-danger' : ''}">${soles(saldo)}</td>
        <td>${badge(situacionEntrega(p))}</td>
        <td class="text-end text-nowrap">
          ${saldo > 0
            ? `<a class="btn btn-sm btn-amarillo" href="pagos.html?pedido=${p.codigo}"><i class="bi bi-cash-coin me-1"></i> Cobrar</a>`
            : `<button type="button" class="btn btn-sm btn-salvia" data-entregar="${p.codigo}"><i class="bi bi-truck me-1"></i> Entregar</button>`}
        </td>
      </tr>`;
    }).join('') : vacio(8, q ? 'No hay pedidos listos que coincidan.' : 'No hay pedidos listos para entrega.', 'bi-box2-heart');
    $$('#tablaListos tr[data-codigo]').forEach((tr) => tr.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) return;
      seleccionado = seleccionado === tr.dataset.codigo ? null : tr.dataset.codigo;
      pintarListos();
    }));
    $$('#tablaListos [data-entregar]').forEach((b) => b.addEventListener('click', () => abrir(b.dataset.entregar)));
  };

  const pintarEntregados = () => {
    const q = $('#enBuscarHistorial').value.trim();
    const todos = DB.pedidos.filter((p) => p.estado === 'Entregado');
    const lista = todos.filter((p) => coincidePedido(p, q)).sort((a, b) => (b.fechas.entregado || '').localeCompare(a.fechas.entregado || ''));
    $('#enTotalEntregados').textContent = q ? `${lista.length} de ${todos.length} entrega(s)` : `${todos.length} pedido(s) entregados al cliente`;
    $('#tablaEntregados').innerHTML = lista.length ? lista.map((p) => {
      const c = clienteDe(p);
      return `<tr>
        <td class="code">${p.codigo}</td>
        <td>${esc(c.institucion || c.nombre)}</td>
        <td>${esc(p.prenda)}</td>
        <td class="text-center">${p.cantidad}</td>
        <td class="text-nowrap">${fechaCorta(p.fechas.entregado)}</td>
        <td>${esc((p.entrega && p.entrega.recibidoPor) || c.nombre)}</td>
        <td class="text-end">${soles(p.total)}</td>
        <td class="text-end"><a class="btn-accion soft-lavanda" href="seguimiento.html?pedido=${p.codigo}" title="Ver seguimiento" aria-label="Ver seguimiento ${p.codigo}"><i class="bi bi-signpost-split"></i></a></td>
      </tr>`;
    }).join('') : vacio(8, 'No se encontraron entregas.', 'bi-truck');
  };

  const pintar = () => {
    pintarResumen();
    pintarListos();
    pintarEntregados();
  };

  const pintarDetalle = () => {
    const p = $('#enPedido').value && buscarPedido($('#enPedido').value);
    const autorizar = $('#enAutorizar');
    const irPagos = $('#enIrPagos');
    if (!p) {
      $('#enDetalle').innerHTML = '<div class="info-cliente soft-azul"><i class="bi bi-hand-index fs-5"></i><span>Selecciona el pedido que se va a entregar.</span></div>';
      autorizar.disabled = true;
      irPagos.classList.add('d-none');
      return;
    }
    const c = clienteDe(p);
    const saldo = saldoDe(p);
    const aviso = saldo > 0
      ? `<div class="info-cliente soft-rosa mb-2"><i class="bi bi-shield-exclamation fs-5"></i><span>Entrega retenida: falta cancelar <strong>${soles(saldo)}</strong>. Registra el pago antes de autorizar la salida.</span></div>`
      : '<div class="info-cliente soft-salvia mb-2"><i class="bi bi-shield-check fs-5"></i><span>Pago completo. Puedes autorizar la salida del almacén.</span></div>';
    $('#enDetalle').innerHTML = `${aviso}
      <ul class="resumen-lista">
        <li><span>Cliente</span><span>${esc(c.nombre)} · DNI ${esc(p.dni)}</span></li>
        <li><span>Prenda</span><span>${esc(p.prenda)} · ${esc(p.tela)} ${esc(p.color)}</span></li>
        <li><span>Cantidad</span><span>${p.cantidad} prendas</span></li>
        <li><span>Listo desde</span><span>${fechaCorta(p.fechas.listo)}</span></li>
        <li><span>Total / pagado</span><span>${soles(p.total)} / ${soles(pagadoDe(p))}</span></li>
        <li class="total"><span>Saldo</span><span class="${saldo > 0 ? 'text-danger' : 'text-salvia'}">${soles(saldo)}</span></li>
      </ul>`;
    if (!$('#enRecibe').dataset.editado) $('#enRecibe').value = c.nombre === 'Cliente no registrado' ? '' : c.nombre;
    autorizar.disabled = saldo > 0;
    irPagos.classList.toggle('d-none', saldo <= 0);
    irPagos.href = `pagos.html?pedido=${p.codigo}`;
  };

  const abrir = (codigo = '') => {
    form.reset();
    form.classList.remove('was-validated');
    delete $('#enRecibe').dataset.editado;
    const lista = listos();
    $('#enPedido').innerHTML = lista.length
      ? '<option value="">Selecciona…</option>' + lista.map((p) => `<option value="${p.codigo}">${p.codigo} · ${esc(clienteDe(p).institucion || clienteDe(p).nombre)} · ${saldoDe(p) > 0 ? 'saldo ' + soles(saldoDe(p)) : 'pagado'}</option>`).join('')
      : '<option value="">No hay pedidos listos para entrega</option>';
    $('#enPedido').value = lista.some((p) => p.codigo === codigo) ? codigo : '';
    $('#enFecha').value = hoyISO();
    $('#enFecha').max = hoyISO();
    pintarDetalle();
    modal.show();
  };

  $('#enPedido').addEventListener('change', pintarDetalle);
  $('#enRecibe').addEventListener('input', (e) => { e.target.dataset.editado = '1'; });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const p = $('#enPedido').value && buscarPedido($('#enPedido').value);
    if (!form.checkValidity() || !p) {
      form.classList.add('was-validated');
      return;
    }
    if (saldoDe(p) > 0) {
      mostrarToast(`No se puede entregar: el pedido tiene un saldo de ${soles(saldoDe(p))}.`, 'error');
      return;
    }
    p.estado = 'Entregado';
    p.fechas.entregado = $('#enFecha').value;
    p.entrega = { recibidoPor: $('#enRecibe').value.trim(), observaciones: $('#enObs').value.trim() };
    guardarDatos();
    seleccionado = null;
    modal.hide();
    pintar();
    pintarNotificaciones();
    mostrarToast(`Pedido <strong>${p.codigo}</strong> entregado a ${esc(p.entrega.recibidoPor)}.`, 'exito');
  });

  $$('[data-nueva-entrega]').forEach((b) => b.addEventListener('click', () => abrir(seleccionado || '')));
  $('#enBuscar').addEventListener('input', pintarListos);
  $('#enBuscarHistorial').addEventListener('input', pintarEntregados);

  pintar();
  const codigo = parametro('pedido');
  const p = codigo && buscarPedido(codigo);
  if (p && p.estado === 'Listo para entrega') {
    seleccionado = p.codigo;
    pintarListos();
    abrir(p.codigo);
  } else if (p) {
    $('#enBuscarHistorial').value = p.codigo;
    pintarEntregados();
    if (p.estado !== 'Entregado') mostrarToast(`El pedido <strong>${p.codigo}</strong> aún está ${esc(p.estado.toLowerCase())}.`, 'info');
  }
}


function filtrarFecha(filas, desde, hasta) {
  return filas.filter((r) => (!desde || (r.fecha && r.fecha >= desde)) && (!hasta || (r.fecha && r.fecha <= hasta)));
}

function sumar(filas, campo) {
  return filas.reduce((a, r) => a + Number(r[campo] || 0), 0);
}

const REPORTES = {
  clientes: {
    titulo: 'Reporte de clientes',
    tile: 'Clientes',
    sub: 'Clientes registrados',
    icono: 'bi-people',
    color: 'lavanda',
    estados: [],
    filas: () => DB.clientes.map((c) => ({ ...c, pedidos: DB.pedidos.filter((p) => p.dni === c.dni).length, estado: '' })),
    columnas: [
      { titulo: 'DNI', valor: (r) => r.dni, clase: 'code' },
      { titulo: 'Cliente', valor: (r) => r.nombre },
      { titulo: 'Institución', valor: (r) => r.institucion || '—' },
      { titulo: 'Teléfono', valor: (r) => r.telefono || '—' },
      { titulo: 'Registro', valor: (r) => fechaCorta(r.fecha), clase: 'text-nowrap' },
      { titulo: 'Pedidos', valor: (r) => r.pedidos, clase: 'text-center' }
    ],
    resumen: (f) => [
      { etiqueta: 'Clientes', valor: f.length, icono: 'bi-people', color: 'lavanda' },
      { etiqueta: 'Con pedidos', valor: f.filter((r) => r.pedidos > 0).length, icono: 'bi-clipboard-check', color: 'azul' },
      { etiqueta: 'Instituciones', valor: f.filter((r) => r.institucion).length, icono: 'bi-building', color: 'salvia' },
      { etiqueta: 'Pedidos totales', valor: sumar(f, 'pedidos'), icono: 'bi-handbag', color: 'amarillo' }
    ]
  },
  pedidos: {
    titulo: 'Reporte de pedidos',
    tile: 'Pedidos',
    sub: 'Pedidos registrados',
    icono: 'bi-clipboard-check',
    color: 'azul',
    estados: ['Registrado', 'En producción', 'Listo para entrega', 'Entregado'],
    filas: () => DB.pedidos.map((p) => ({ ...p, cliente: clienteDe(p) })),
    columnas: [
      { titulo: 'Código', valor: (r) => r.codigo, clase: 'code' },
      { titulo: 'Fecha', valor: (r) => fechaCorta(r.fecha), clase: 'text-nowrap' },
      { titulo: 'DNI', valor: (r) => r.dni },
      { titulo: 'Cliente', valor: (r) => r.cliente.institucion || r.cliente.nombre },
      { titulo: 'Prenda', valor: (r) => `${r.prenda} · ${r.tela} ${r.color.toLowerCase()}` },
      { titulo: 'Cantidad', valor: (r) => r.cantidad, clase: 'text-center' },
      { titulo: 'Total', valor: (r) => soles(r.total), clase: 'text-end' },
      { titulo: 'Estado', valor: (r) => r.estado, badge: true }
    ],
    resumen: (f) => [
      { etiqueta: 'Pedidos', valor: f.length, icono: 'bi-clipboard-check', color: 'lavanda' },
      { etiqueta: 'Prendas solicitadas', valor: sumar(f, 'cantidad'), icono: 'bi-handbag', color: 'azul' },
      { etiqueta: 'Monto total', valor: soles(sumar(f, 'total')), icono: 'bi-cash-stack', color: 'amarillo' },
      { etiqueta: 'Entregados', valor: f.filter((r) => r.estado === 'Entregado').length, icono: 'bi-check2-circle', color: 'salvia' }
    ]
  },
  dia: {
    titulo: 'Reporte de pedidos del día',
    tile: 'Pedidos del día',
    sub: 'Pedidos registrados hoy',
    icono: 'bi-calendar-check',
    color: 'salvia',
    estados: ['Registrado', 'En producción', 'Listo para entrega', 'Entregado'],
    alElegir: () => { $('#fDesde').value = hoyISO(); $('#fHasta').value = hoyISO(); },
    filas: () => REPORTES.pedidos.filas(),
    get columnas() { return REPORTES.pedidos.columnas; },
    resumen: (f) => REPORTES.pedidos.resumen(f)
  },
  produccion: {
    titulo: 'Reporte de producción',
    tile: 'Producción',
    sub: 'Órdenes de trabajo',
    icono: 'bi-scissors',
    color: 'amarillo',
    estados: ['Corte', 'Costura', 'Estampado', 'Acabado', 'Listo para entrega', 'Entregado'],
    filas: () => DB.pedidos.filter((p) => p.ot).map((p) => ({ ...p, cliente: clienteDe(p), fecha: p.fechas.ot, estado: p.estado === 'En producción' ? etapaActual(p) : p.estado })),
    columnas: [
      { titulo: 'Orden', valor: (r) => r.ot, clase: 'code' },
      { titulo: 'Generada', valor: (r) => fechaCorta(r.fecha), clase: 'text-nowrap' },
      { titulo: 'Pedido', valor: (r) => r.codigo },
      { titulo: 'Cliente', valor: (r) => r.cliente.institucion || r.cliente.nombre },
      { titulo: 'Prenda', valor: (r) => r.prenda },
      { titulo: 'Cantidad', valor: (r) => r.cantidad, clase: 'text-center' },
      { titulo: 'Avance', valor: (r) => avanceDe(r) + '%', clase: 'text-center' },
      { titulo: 'Etapa', valor: (r) => r.estado, badge: true }
    ],
    resumen: (f) => [
      { etiqueta: 'Órdenes', valor: f.length, icono: 'bi-file-earmark-text', color: 'lavanda' },
      { etiqueta: 'En taller', valor: f.filter((r) => ETAPAS.includes(r.estado)).length, icono: 'bi-scissors', color: 'azul' },
      { etiqueta: 'Prendas fabricadas', valor: sumar(f.filter((r) => !ETAPAS.includes(r.estado)), 'cantidad'), icono: 'bi-handbag', color: 'salvia' },
      { etiqueta: 'Listas para entrega', valor: f.filter((r) => r.estado === 'Listo para entrega').length, icono: 'bi-box2-heart', color: 'amarillo' }
    ]
  },
  insumos: {
    titulo: 'Reporte de insumos utilizados',
    tile: 'Insumos utilizados',
    sub: 'Consumo por orden de trabajo',
    icono: 'bi-box-seam',
    color: 'lavanda',
    estados: CATEGORIAS_INSUMO,
    filas: () => DB.movimientos.filter((m) => m.tipo === 'Salida').map((m) => {
      const i = buscarInsumo(m.codigo) || { nombre: m.codigo, color: '', unidad: '', categoria: '' };
      return { ...m, insumo: i, estado: i.categoria };
    }),
    columnas: [
      { titulo: 'Fecha', valor: (r) => fechaCorta(r.fecha), clase: 'text-nowrap' },
      { titulo: 'Orden', valor: (r) => r.referencia, clase: 'code' },
      { titulo: 'Pedido', valor: (r) => r.pedido },
      { titulo: 'Insumo', valor: (r) => nombreInsumo(r.insumo) },
      { titulo: 'Categoría', valor: (r) => r.insumo.categoria },
      { titulo: 'Cantidad', valor: (r) => `${r.cantidad} ${r.insumo.unidad}`, clase: 'text-center' }
    ],
    resumen: (f) => [
      { etiqueta: 'Consumos registrados', valor: f.length, icono: 'bi-journal-text', color: 'lavanda' },
      { etiqueta: 'Órdenes atendidas', valor: new Set(f.map((r) => r.referencia)).size, icono: 'bi-scissors', color: 'azul' },
      { etiqueta: 'Metros de tela', valor: redondear(sumar(f.filter((r) => r.insumo.categoria === 'Tela'), 'cantidad'), 1) + ' m', icono: 'bi-rulers', color: 'salvia' },
      { etiqueta: 'Insumos distintos', valor: new Set(f.map((r) => r.codigo)).size, icono: 'bi-box-seam', color: 'amarillo' }
    ]
  },
  stock: {
    titulo: 'Reporte de stock e inventario',
    tile: 'Inventario',
    sub: 'Stock actual y mínimos',
    icono: 'bi-clipboard-data',
    color: 'rosa',
    estados: ['Normal', 'Bajo', 'Crítico'],
    sinFechas: true,
    filas: () => DB.insumos.map((i) => ({ ...i, estado: estadoInsumo(i), fecha: null })),
    columnas: [
      { titulo: 'Código', valor: (r) => r.codigo, clase: 'code' },
      { titulo: 'Insumo', valor: (r) => nombreInsumo(r) },
      { titulo: 'Categoría', valor: (r) => r.categoria },
      { titulo: 'Stock', valor: (r) => `${r.stock} ${r.unidad}`, clase: 'text-center' },
      { titulo: 'Mínimo', valor: (r) => `${r.minimo} ${r.unidad}`, clase: 'text-center' },
      { titulo: 'Estado', valor: (r) => r.estado, badge: true }
    ],
    resumen: (f) => [
      { etiqueta: 'Insumos', valor: f.length, icono: 'bi-box-seam', color: 'lavanda' },
      { etiqueta: 'Stock normal', valor: f.filter((r) => r.estado === 'Normal').length, icono: 'bi-check2-circle', color: 'salvia' },
      { etiqueta: 'Stock bajo', valor: f.filter((r) => r.estado === 'Bajo').length, icono: 'bi-exclamation-triangle', color: 'amarillo' },
      { etiqueta: 'Nivel crítico', valor: f.filter((r) => r.estado === 'Crítico').length, icono: 'bi-exclamation-octagon', color: 'rosa' }
    ]
  },
  pagos: {
    titulo: 'Reporte de pagos y saldos',
    tile: 'Pagos',
    sub: 'Adelantos y cancelaciones',
    icono: 'bi-cash-coin',
    color: 'salvia',
    estados: ['Adelanto', 'Abono', 'Cancelación'],
    filas: () => DB.pedidos.flatMap((p) => p.pagos.map((x) => ({ ...x, codigo: p.codigo, dni: p.dni, cliente: clienteDe(p), estado: x.tipo }))),
    columnas: [
      { titulo: 'Fecha', valor: (r) => fechaCorta(r.fecha), clase: 'text-nowrap' },
      { titulo: 'Recibo', valor: (r) => r.recibo, clase: 'code' },
      { titulo: 'Pedido', valor: (r) => r.codigo },
      { titulo: 'DNI', valor: (r) => r.dni },
      { titulo: 'Cliente', valor: (r) => r.cliente.nombre },
      { titulo: 'Método', valor: (r) => r.metodo },
      { titulo: 'Monto', valor: (r) => soles(r.monto), clase: 'text-end' },
      { titulo: 'Tipo', valor: (r) => r.tipo, badge: true }
    ],
    resumen: (f) => [
      { etiqueta: 'Pagos registrados', valor: f.length, icono: 'bi-receipt', color: 'lavanda' },
      { etiqueta: 'Monto cobrado', valor: soles(sumar(f, 'monto')), icono: 'bi-cash-stack', color: 'salvia' },
      { etiqueta: 'Adelantos', valor: f.filter((r) => r.tipo === 'Adelanto').length, icono: 'bi-wallet2', color: 'azul' },
      { etiqueta: 'Saldo por cobrar', valor: soles(DB.pedidos.reduce((a, p) => a + saldoDe(p), 0)), icono: 'bi-hourglass-split', color: 'rosa' }
    ]
  },
  entregas: {
    titulo: 'Reporte de prendas entregadas',
    tile: 'Prendas entregadas',
    sub: 'Entregas y salidas del almacén',
    icono: 'bi-truck',
    color: 'azul',
    estados: ['Entregado', 'Entrega autorizada', 'Retenido por saldo'],
    filas: () => DB.pedidos.filter((p) => p.estado === 'Entregado' || p.estado === 'Listo para entrega').map((p) => ({
      ...p,
      cliente: clienteDe(p),
      fecha: p.fechas.entregado || p.fechas.listo,
      estado: p.estado === 'Entregado' ? 'Entregado' : situacionEntrega(p)
    })),
    columnas: [
      { titulo: 'Fecha', valor: (r) => fechaCorta(r.fecha), clase: 'text-nowrap' },
      { titulo: 'Pedido', valor: (r) => r.codigo, clase: 'code' },
      { titulo: 'Cliente', valor: (r) => r.cliente.institucion || r.cliente.nombre },
      { titulo: 'Prendas', valor: (r) => r.cantidad, clase: 'text-center' },
      { titulo: 'Total', valor: (r) => soles(r.total), clase: 'text-end' },
      { titulo: 'Pagado', valor: (r) => soles(pagadoDe(r)), clase: 'text-end' },
      { titulo: 'Estado', valor: (r) => r.estado, badge: true }
    ],
    resumen: (f) => [
      { etiqueta: 'Registros', valor: f.length, icono: 'bi-truck', color: 'lavanda' },
      { etiqueta: 'Prendas entregadas', valor: sumar(f.filter((r) => r.estado === 'Entregado'), 'cantidad'), icono: 'bi-bag-check', color: 'salvia' },
      { etiqueta: 'Autorizadas', valor: f.filter((r) => r.estado === 'Entrega autorizada').length, icono: 'bi-shield-check', color: 'azul' },
      { etiqueta: 'Retenidas por saldo', valor: f.filter((r) => r.estado === 'Retenido por saldo').length, icono: 'bi-shield-exclamation', color: 'rosa' }
    ]
  }
};

function iniciarReportes() {
  const selTipo = $('#fTipo');
  const selEstado = $('#fEstado');
  let filas = [];

  selTipo.innerHTML = Object.entries(REPORTES).map(([k, r]) => `<option value="${k}">${r.tile}</option>`).join('');

  $('#tilesReportes').innerHTML = Object.entries(REPORTES).map(([k, r]) => `
    <button type="button" class="module-tile border-0" data-reporte="${k}">
      <span class="tile-icon soft-${r.color}"><i class="bi ${r.icono}"></i></span>
      <span class="tile-title">${r.tile}</span>
      <span class="tile-sub">${r.sub}</span>
    </button>`).join('');

  const cargarEstados = () => {
    const r = REPORTES[selTipo.value];
    selEstado.innerHTML = '<option value="">Todos</option>' + r.estados.map((e) => `<option>${e}</option>`).join('');
    selEstado.disabled = !r.estados.length;
    $('#fDesde').disabled = !!r.sinFechas;
    $('#fHasta').disabled = !!r.sinFechas;
    $$('[data-reporte]').forEach((t) => t.classList.toggle('active', t.dataset.reporte === selTipo.value));
  };

  const filtrar = () => {
    const r = REPORTES[selTipo.value];
    const desde = r.sinFechas ? '' : $('#fDesde').value;
    const hasta = r.sinFechas ? '' : $('#fHasta').value;
    const malas = desde && hasta && desde > hasta;
    $('#errorFechas').classList.toggle('d-none', !malas);
    if (malas) return false;
    filas = filtrarFecha(r.filas(), desde, hasta).filter((x) => !selEstado.value || x.estado === selEstado.value);
    filas.sort((a, b) => String(b.fecha || '').localeCompare(String(a.fecha || '')));

    $('#resumenReporte').innerHTML = r.resumen(filas).map(htmlStat).join('');
    $('#tituloReporte').textContent = r.titulo;
    $('#infoResultados').textContent = `Mostrando ${filas.length} registro${filas.length === 1 ? '' : 's'}`;
    $('#theadReporte').innerHTML = '<tr>' + r.columnas.map((c) => `<th class="${c.clase || ''}">${c.titulo}</th>`).join('') + '</tr>';
    $('#tbodyReporte').innerHTML = filas.length
      ? filas.map((x) => '<tr>' + r.columnas.map((c) => {
        const v = c.valor(x);
        return `<td class="${c.clase || ''}">${c.badge ? badge(v) : esc(v)}</td>`;
      }).join('') + '</tr>').join('')
      : vacio(r.columnas.length, 'No se encontraron registros con los filtros seleccionados.', 'bi-search');
    $('#printEncabezado').innerHTML = `
      <h2 class="h4 mb-1">${esc(DB.config.empresa.nombre)} · ${esc(r.titulo)}</h2>
      <p class="small text-muted mb-0">Generado el ${fechaCorta(hoyISO())}${desde ? ` · Desde ${fechaCorta(desde)}` : ''}${hasta ? ` · Hasta ${fechaCorta(hasta)}` : ''}${selEstado.value ? ` · Estado: ${esc(selEstado.value)}` : ''}</p>`;
    return true;
  };

  const elegir = (tipo) => {
    selTipo.value = tipo;
    $('#fDesde').value = '';
    $('#fHasta').value = '';
    cargarEstados();
    if (REPORTES[tipo].alElegir) REPORTES[tipo].alElegir();
    filtrar();
  };

  $$('[data-reporte]').forEach((t) => t.addEventListener('click', () => {
    elegir(t.dataset.reporte);
    $('#tituloReporte').scrollIntoView({ behavior: 'smooth', block: 'center' });
  }));
  selTipo.addEventListener('change', () => elegir(selTipo.value));

  $('#formFiltros').addEventListener('submit', (e) => {
    e.preventDefault();
    if (filtrar()) mostrarToast(`Reporte generado: ${filas.length} registro(s).`);
  });

  $('#btnLimpiar').addEventListener('click', () => {
    $('#fDesde').value = '';
    $('#fHasta').value = '';
    selEstado.value = '';
    filtrar();
    mostrarToast('Filtros restablecidos.');
  });

  $('#btnExportar').addEventListener('click', () => {
    if (!filas.length) {
      mostrarToast('No hay registros para exportar con los filtros actuales.', 'aviso');
      return;
    }
    if ($('#fFormato').value === 'pdf') {
      window.print();
      return;
    }
    const r = REPORTES[selTipo.value];
    const celda = (v) => `"${String(v).replace(/"/g, '""')}"`;
    const lineas = [
      r.columnas.map((c) => celda(c.titulo)).join(','),
      ...filas.map((x) => r.columnas.map((c) => celda(c.valor(x))).join(','))
    ];
    const blob = new Blob(['﻿' + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const enlace = document.createElement('a');
    enlace.href = url;
    enlace.download = `delitex-${selTipo.value}-${hoyISO()}.csv`;
    document.body.appendChild(enlace);
    enlace.click();
    enlace.remove();
    URL.revokeObjectURL(url);
    mostrarToast(`Se exportaron ${filas.length} registro(s) a CSV.`, 'exito');
  });

  elegir(parametro('tipo') && REPORTES[parametro('tipo')] ? parametro('tipo') : 'pedidos');
}


function iniciarUsuarios() {
  const modal = bootstrap.Modal.getOrCreateInstance($('#modalUsuario'));
  let seleccionado = null;
  let editando = null;

  $('#uRol').innerHTML = ROLES.map((r) => `<option>${r}</option>`).join('');

  const pintar = () => {
    $('#tablaUsuarios').innerHTML = DB.usuarios.map((u) => `
      <tr class="fila-seleccionable ${seleccionado === u.usuario ? 'seleccionada' : ''}" data-usuario="${esc(u.usuario)}">
        <td class="code">${esc(u.usuario)}</td>
        <td><div class="d-flex align-items-center gap-2"><span class="avatar soft-lavanda" style="width:32px;height:32px;font-size:.72rem">${esc(iniciales(u.nombre))}</span>${esc(u.nombre)}</div></td>
        <td>${esc(u.correo)}</td>
        <td>${esc(u.rol)}</td>
        <td>${badge(u.estado)}</td>
        <td class="text-end text-nowrap">
          <button type="button" class="btn-accion soft-amarillo" data-editar="${esc(u.usuario)}" title="Modificar" aria-label="Modificar ${esc(u.usuario)}"><i class="bi bi-pencil-square"></i></button>
          <button type="button" class="btn-accion soft-rosa" data-eliminar="${esc(u.usuario)}" title="Eliminar" aria-label="Eliminar ${esc(u.usuario)}"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`).join('');
    $$('#tablaUsuarios tr[data-usuario]').forEach((tr) => tr.addEventListener('click', (e) => {
      if (e.target.closest('button')) return;
      seleccionado = seleccionado === tr.dataset.usuario ? null : tr.dataset.usuario;
      pintar();
    }));
    $$('[data-editar]').forEach((b) => b.addEventListener('click', () => abrir(buscar(b.dataset.editar))));
    $$('[data-eliminar]').forEach((b) => b.addEventListener('click', () => eliminar(buscar(b.dataset.eliminar))));
  };

  const buscar = (usuario) => DB.usuarios.find((u) => u.usuario === usuario);

  const abrir = (u) => {
    editando = u ? u.usuario : null;
    const form = $('#formUsuario');
    form.reset();
    form.classList.remove('was-validated');
    $('#modalUsuarioLabel').textContent = u ? `Modificar ${u.usuario}` : 'Nuevo usuario';
    $('#uUsuario').value = u ? u.usuario : '';
    $('#uUsuario').readOnly = !!u;
    $('#uNombre').value = u ? u.nombre : '';
    $('#uCorreo').value = u ? u.correo : '';
    $('#uRol').value = u ? u.rol : 'Recepción';
    $('#uEstado').value = u ? u.estado : 'Activo';
    $('#uClave').required = !u;
    $('#uClaveAyuda').textContent = u ? 'Déjala vacía para mantener la contraseña actual.' : 'Mínimo 6 caracteres.';
    modal.show();
  };

  const eliminar = async (u) => {
    if (u.usuario === usuarioActual().usuario) {
      mostrarToast('No puedes eliminar el usuario con el que iniciaste sesión.', 'error');
      return;
    }
    if (!(await confirmar(`¿Eliminar al usuario <strong>${esc(u.usuario)}</strong>?`, 'Eliminar'))) return;
    DB.usuarios = DB.usuarios.filter((x) => x.usuario !== u.usuario);
    seleccionado = null;
    guardarDatos();
    pintar();
    mostrarToast('Usuario eliminado.', 'exito');
  };

  const requerir = () => {
    const u = seleccionado && buscar(seleccionado);
    if (!u) mostrarToast('Selecciona un usuario de la tabla.', 'aviso');
    return u;
  };

  $$('[data-nuevo-usuario]').forEach((b) => b.addEventListener('click', () => abrir(null)));
  $('#btnModificarUsuario').addEventListener('click', () => { const u = requerir(); if (u) abrir(u); });
  $('#btnEliminarUsuario').addEventListener('click', () => { const u = requerir(); if (u) eliminar(u); });

  $('#formUsuario').addEventListener('submit', (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    const datos = {
      usuario: $('#uUsuario').value.trim(),
      nombre: $('#uNombre').value.trim(),
      correo: $('#uCorreo').value.trim().toLowerCase(),
      rol: $('#uRol').value,
      estado: $('#uEstado').value
    };
    if (DB.usuarios.some((u) => u.correo === datos.correo && u.usuario !== editando)) {
      mostrarToast('Ese correo ya pertenece a otro usuario.', 'error');
      return;
    }
    if (editando) {
      const u = buscar(editando);
      if (u.usuario === usuarioActual().usuario && datos.estado !== 'Activo') {
        mostrarToast('No puedes desactivar tu propio usuario.', 'error');
        return;
      }
      Object.assign(u, datos);
      if ($('#uClave').value) u.clave = $('#uClave').value;
      mostrarToast('Usuario actualizado.', 'exito');
    } else {
      if (buscar(datos.usuario)) {
        mostrarToast('Ese nombre de usuario ya existe.', 'error');
        return;
      }
      DB.usuarios.push({ ...datos, clave: $('#uClave').value });
      mostrarToast(`Usuario <strong>${esc(datos.usuario)}</strong> creado.`, 'exito');
    }
    guardarDatos();
    modal.hide();
    pintar();
    iniciarPlantillaUsuario();
  });

  pintar();
}

function iniciarPlantillaUsuario() {
  const u = usuarioActual();
  $('#userName').textContent = u.nombre;
  $('#userEmail').textContent = u.correo;
  $('#userAvatar').textContent = iniciales(u.nombre);
  $('#footerRol').textContent = u.rol;
}


function iniciarConfiguracion() {
  const cfg = DB.config;
  const u = usuarioActual();

  $('#cfNombre').value = cfg.empresa.nombre;
  $('#cfRuc').value = cfg.empresa.ruc;
  $('#cfDireccion').value = cfg.empresa.direccion;
  $('#cfTelefono').value = cfg.empresa.telefono;
  $('#cfCorreo').value = cfg.empresa.correo;
  $('#cfAdelanto').value = cfg.pedidos.adelantoMinimo;
  $('#cfDias').value = cfg.pedidos.diasEntrega;
  $('#cfCritico').value = cfg.inventario.porcentajeCritico;

  $('#cfPrendas').innerHTML = Object.entries(cfg.prendas).map(([nombre, p], k) => `
    <tr>
      <td class="fw-semibold">${esc(nombre)}</td>
      <td>${esc(p.tela)}</td>
      <td><input type="number" class="form-control form-control-sm" style="max-width:120px" min="0" step="0.5" value="${p.precio}" data-prenda-precio="${k}" aria-label="Precio de ${esc(nombre)}" required></td>
      <td><input type="number" class="form-control form-control-sm" style="max-width:120px" min="0.1" step="0.1" value="${p.metros}" data-prenda-metros="${k}" aria-label="Tela por prenda de ${esc(nombre)}" required></td>
    </tr>`).join('');
  $('#cfEstampados').innerHTML = Object.entries(cfg.estampados).map(([nombre, v], k) => `
    <tr>
      <td class="fw-semibold">${esc(nombre)}</td>
      <td><input type="number" class="form-control form-control-sm" style="max-width:120px" min="0" step="0.5" value="${v}" data-estampado="${k}" aria-label="Recargo de ${esc(nombre)}" required></td>
    </tr>`).join('');

  $('#cfAvatar').textContent = iniciales(u.nombre);
  $('#cfPerfilNombre').textContent = u.nombre;
  $('#cfPerfilRol').textContent = `${u.rol} · ${u.correo}`;
  $('#cfMiNombre').value = u.nombre;
  $('#cfMiUsuario').value = u.usuario;
  $('#cfMiCorreo').value = u.correo;

  const pestañas = { general: '#tabGeneral', pedidos: '#tabPedidos', inventario: '#tabInventario', cuenta: '#tabCuenta', clave: '#tabCuenta', datos: '#tabDatos' };
  const abrirPestaña = () => {
    const destino = pestañas[location.hash.slice(1)];
    if (!destino) return;
    bootstrap.Tab.getOrCreateInstance($(`[data-bs-target="${destino}"]`)).show();
    if (location.hash === '#clave') setTimeout(() => $('#cfClaveActual').focus(), 200);
  };
  abrirPestaña();
  window.addEventListener('hashchange', abrirPestaña);

  $('#btnCambiarLogo').addEventListener('click', () => mostrarToast('La carga del logo estará disponible cuando el sistema tenga servidor.', 'info'));

  $('#btnCambiarClave').addEventListener('click', () => {
    const actual = $('#cfClaveActual');
    const nueva = $('#cfClaveNueva');
    const conf = $('#cfClaveConfirmar');
    actual.classList.toggle('is-invalid', actual.value !== u.clave);
    nueva.classList.toggle('is-invalid', nueva.value.length < 6);
    conf.classList.toggle('is-invalid', conf.value !== nueva.value || !conf.value);
    if (actual.value !== u.clave || nueva.value.length < 6 || conf.value !== nueva.value) return;
    u.clave = nueva.value;
    guardarDatos();
    [actual, nueva, conf].forEach((i) => { i.value = ''; i.classList.remove('is-invalid'); });
    mostrarToast('Tu contraseña fue actualizada.', 'exito');
  });

  $('#btnRestablecer').addEventListener('click', async () => {
    if (!(await confirmar('¿Restablecer todos los datos de demostración? Se perderán los pedidos, pagos y cambios registrados.', 'Restablecer'))) return;
    restablecerDatos();
    location.reload();
  });

  $('#formConfig').addEventListener('submit', (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      const invalido = $(':invalid', form);
      const panel = invalido && invalido.closest('.tab-pane');
      if (panel) bootstrap.Tab.getOrCreateInstance($(`[data-bs-target="#${panel.id}"]`)).show();
      mostrarToast('Revisa los campos marcados.', 'aviso');
      return;
    }
    cfg.empresa = {
      nombre: $('#cfNombre').value.trim(),
      ruc: $('#cfRuc').value.trim(),
      direccion: $('#cfDireccion').value.trim(),
      telefono: $('#cfTelefono').value.trim(),
      correo: $('#cfCorreo').value.trim()
    };
    cfg.pedidos.adelantoMinimo = parseFloat($('#cfAdelanto').value);
    cfg.pedidos.diasEntrega = parseInt($('#cfDias').value, 10);
    cfg.inventario.porcentajeCritico = parseFloat($('#cfCritico').value);
    const prendas = Object.keys(cfg.prendas);
    prendas.forEach((nombre, k) => {
      cfg.prendas[nombre].precio = parseFloat($(`[data-prenda-precio="${k}"]`).value);
      cfg.prendas[nombre].metros = parseFloat($(`[data-prenda-metros="${k}"]`).value);
    });
    Object.keys(cfg.estampados).forEach((nombre, k) => {
      cfg.estampados[nombre] = parseFloat($(`[data-estampado="${k}"]`).value);
    });
    const nombre = $('#cfMiNombre').value.trim();
    const correo = $('#cfMiCorreo').value.trim().toLowerCase();
    if (nombre) u.nombre = nombre;
    if (correo) u.correo = correo;
    guardarDatos();
    iniciarPlantillaUsuario();
    $('#cfPerfilNombre').textContent = u.nombre;
    $('#cfAvatar').textContent = iniciales(u.nombre);
    pintarNotificaciones();
    mostrarToast('Cambios guardados correctamente.', 'exito');
  });
}
