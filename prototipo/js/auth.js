/* ==========================================================================
   Control de acceso: inicio de sesión, sesión, roles y permisos.

   Se carga en el <head> de todas las páginas, después de datos.js y antes de
   que se pinte el contenido, para bloquear las páginas sin permiso.

   AVISO: este prototipo no tiene servidor. Todo lo que está aquí se ejecuta en
   el navegador, así que una persona con conocimientos técnicos puede leer y
   modificar este código y el localStorage. Las medidas de este archivo (hash de
   contraseñas, sesión con token, sellos de integridad y bloqueo de páginas)
   sirven para la demostración y para evitar cambios casuales, pero NO son
   seguridad real. En producción, la autenticación, la sesión y cada permiso se
   deben validar en un backend (ver README, sección "Seguridad").
   ========================================================================== */

const CLAVE_SESIONES = 'delitex-sesiones';        // simula la tabla de sesiones del servidor
const CLAVE_INTENTOS = 'delitex-intentos';        // intentos fallidos de inicio de sesión
const CLAVE_RECUPERACION = 'delitex-recuperacion'; // enlaces de restablecimiento de contraseña
const VERSION_SEGURIDAD = 1;
const SECRETO_DEMO = 'delitex-prototipo-sello-2026'; // en el navegador no existen secretos: solo demostración
const HORAS_SESION = 8;
const MAX_INTENTOS = 5;
const MINUTOS_BLOQUEO = 5;
const ITERACIONES_HASH = 2000;

const ROL_DUENA = 'Dueña';
const ROL_ADMIN = 'Administradora';
const ROL_CONFECCION = 'Confeccionista';

const PAGINAS_PUBLICAS = ['index', 'registro', 'recuperar', 'restablecer'];

/* ---------- Accesos de prueba por perfil (TEMPORAL) ----------
   Cada botón del login se asocia a un perfil. Las credenciales NO se muestran en
   pantalla: al elegir el perfil, el login las carga en el formulario.
   "cuenta" es la cuenta registrada (datos.js) con la que se abre la sesión, así el
   panel y los permisos siguen siendo exactamente los del rol (tabla PERMISOS).
   Al conectar una base de datos, se elimina esta tabla y se valida contra el servidor. */
const PERFILES_PRUEBA = {
  [ROL_DUENA]:      { usuario: 'dueña',          clave: '1234', cuenta: 'mquispe' },
  [ROL_ADMIN]:      { usuario: 'admin',          clave: '1234', cuenta: 'lrojas' },
  [ROL_CONFECCION]: { usuario: 'confeccionista', clave: '1234', cuenta: 'jtaipe' }
};

const MODULOS = {
  dashboard: 'Dashboard',
  reportes: 'Reportes',
  clientes: 'Clientes',
  pedidos: 'Pedidos',
  seguimiento: 'Seguimiento de pedidos',
  produccion: 'Producción',
  insumos: 'Insumos',
  almacen: 'Almacén',
  alertas: 'Alertas de stock',
  pagos: 'Pagos',
  entregas: 'Entregas',
  usuarios: 'Usuarios',
  configuracion: 'Configuración'
};

/* ---------- Tabla única de permisos ----------
   modulos: páginas a las que puede entrar el rol.
   acciones: operaciones permitidas dentro de los módulos ('*' = todas).
   rolesAsignables: roles que puede dar al crear o modificar cuentas. */
const PERMISOS = {
  [ROL_DUENA]: {
    inicio: 'dashboard.html',
    tablero: 'Dashboard general',
    modulos: Object.keys(MODULOS),
    acciones: ['*'],
    rolesAsignables: [ROL_ADMIN, ROL_CONFECCION]
  },
  [ROL_ADMIN]: {
    inicio: 'dashboard.html',
    tablero: 'Dashboard operativo',
    // "usuarios" solo si la dueña lo permite en Configuración → Seguridad
    modulos: ['dashboard', 'reportes', 'clientes', 'pedidos', 'seguimiento', 'produccion', 'insumos', 'almacen', 'alertas', 'pagos', 'entregas', 'usuarios'],
    acciones: [
      'pedidos.gestionar', 'clientes.gestionar', 'pagos.ver', 'pagos.registrar', 'entregas.registrar',
      'insumos.gestionar', 'almacen.registrar', 'produccion.generar', 'produccion.asignar',
      'produccion.avanzar', 'produccion.verTodas', 'seguimiento.verTodos', 'usuarios.gestionar'
    ],
    rolesAsignables: [ROL_CONFECCION]
  },
  [ROL_CONFECCION]: {
    inicio: 'produccion.html',
    tablero: 'Mis órdenes de trabajo',
    modulos: ['produccion', 'seguimiento', 'insumos'],
    acciones: ['produccion.avanzarPropias'],
    rolesAsignables: []
  }
};

/* ---------- SHA-256 (síncrono, sin dependencias) ---------- */
const sha256 = (() => {
  const K = new Uint32Array([
    0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
    0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
    0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
    0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
    0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
    0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
    0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
    0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
  ]);
  const rotr = (x, n) => (x >>> n) | (x << (32 - n));
  return (texto) => {
    const bytes = new TextEncoder().encode(String(texto));
    const largo = bytes.length;
    const total = ((largo + 9 + 63) >> 6) << 6;
    const datos = new Uint8Array(total);
    datos.set(bytes);
    datos[largo] = 0x80;
    const vista = new DataView(datos.buffer);
    vista.setUint32(total - 8, Math.floor((largo * 8) / 0x100000000));
    vista.setUint32(total - 4, (largo * 8) >>> 0);
    const H = new Uint32Array([0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]);
    const W = new Uint32Array(64);
    for (let i = 0; i < total; i += 64) {
      for (let t = 0; t < 16; t++) W[t] = vista.getUint32(i + t * 4);
      for (let t = 16; t < 64; t++) {
        const s0 = rotr(W[t - 15], 7) ^ rotr(W[t - 15], 18) ^ (W[t - 15] >>> 3);
        const s1 = rotr(W[t - 2], 17) ^ rotr(W[t - 2], 19) ^ (W[t - 2] >>> 10);
        W[t] = (W[t - 16] + s0 + W[t - 7] + s1) >>> 0;
      }
      let [a, b, c, d, e, f, g, h] = H;
      for (let t = 0; t < 64; t++) {
        const t1 = (h + (rotr(e, 6) ^ rotr(e, 11) ^ rotr(e, 25)) + ((e & f) ^ (~e & g)) + K[t] + W[t]) >>> 0;
        const t2 = ((rotr(a, 2) ^ rotr(a, 13) ^ rotr(a, 22)) + ((a & b) ^ (a & c) ^ (b & c))) >>> 0;
        h = g; g = f; f = e; e = (d + t1) >>> 0; d = c; c = b; b = a; a = (t1 + t2) >>> 0;
      }
      [a, b, c, d, e, f, g, h].forEach((v, k) => { H[k] = (H[k] + v) >>> 0; });
    }
    return [...H].map((x) => x.toString(16).padStart(8, '0')).join('');
  };
})();

function textoAleatorio(bytes = 24) {
  const a = new Uint8Array(bytes);
  crypto.getRandomValues(a);
  return [...a].map((x) => x.toString(16).padStart(2, '0')).join('');
}

function firmar(texto) {
  return sha256(SECRETO_DEMO + '|' + texto);
}

function leerJSON(clave, defecto) {
  try {
    return JSON.parse(localStorage.getItem(clave)) || defecto;
  } catch (e) {
    return defecto;
  }
}

function guardarJSON(clave, valor) {
  try {
    localStorage.setItem(clave, JSON.stringify(valor));
  } catch (e) {}
}

/* ---------- Contraseñas: nunca se guardan en texto plano ---------- */
function hashClave(clave, sal) {
  let h = sha256(sal + '|' + clave);
  for (let i = 0; i < ITERACIONES_HASH; i++) h = sha256(sal + h);
  return h;
}

function establecerClave(u, clave) {
  u.sal = textoAleatorio(16);
  u.claveHash = hashClave(clave, u.sal);
  delete u.clave;
  sellarUsuario(u);
}

function verificarClave(u, clave) {
  return !!(u && u.sal && u.claveHash && hashClave(clave, u.sal) === u.claveHash);
}

/* ---------- Sello de integridad de cada cuenta ----------
   Une rol, estado y contraseña: si alguien cambia el rol desde el navegador
   (localStorage o consola), el sello deja de coincidir y la cuenta se rechaza. */
function selloDe(u) {
  return firmar(['usuario', u.id, u.usuario, u.rol, u.estado, u.claveHash, u.sal, u.propietaria ? 1 : 0].join('|'));
}

function sellarUsuario(u) {
  u.sello = selloDe(u);
}

function usuarioIntegro(u) {
  return !!u && !!PERMISOS[u.rol] && u.sello === selloDe(u) && (u.rol !== ROL_DUENA || u.propietaria === true);
}

function selloConfigSeguridad(s) {
  return firmar('config|' + (s.adminGestionaUsuarios ? 1 : 0));
}

function adminGestionaUsuarios() {
  const s = DB.config.seguridad;
  return !!(s && s.sello === selloConfigSeguridad(s) && s.adminGestionaUsuarios);
}

function cambiarAdminGestionaUsuarios(valor) {
  DB.config.seguridad = { adminGestionaUsuarios: !!valor };
  DB.config.seguridad.sello = selloConfigSeguridad(DB.config.seguridad);
}

function siguienteIdUsuario() {
  const n = DB.usuarios.map((u) => parseInt(String(u.id || '').replace(/\D/g, ''), 10) || 0);
  return 'USR-' + String((n.length ? Math.max(...n) : 0) + 1).padStart(3, '0');
}

/* ---------- Conversión de los datos guardados al esquema con roles ----------
   Se ejecuta una sola vez (o después de "Restablecer datos"): convierte los roles
   anteriores, cifra las contraseñas y asigna las órdenes de trabajo. */
function asegurarCuentas() {
  if (DB.seguridad === VERSION_SEGURIDAD) return;

  const cuentasDemo = { lrojas: ROL_ADMIN, calva: ROL_ADMIN, jtaipe: ROL_CONFECCION };
  const rolAnterior = { 'Administrador': ROL_ADMIN, 'Recepción': ROL_CONFECCION, 'Taller': ROL_CONFECCION, 'Almacén': ROL_CONFECCION };

  DB.usuarios.forEach((u) => {
    if (!PERMISOS[u.rol]) u.rol = cuentasDemo[u.usuario] || rolAnterior[u.rol] || ROL_CONFECCION;
  });

  // una sola cuenta principal (dueña)
  const duenas = DB.usuarios.filter((u) => u.rol === ROL_DUENA);
  const principal = duenas[0] || DB.usuarios.find((u) => u.usuario === 'mquispe') || DB.usuarios.find((u) => u.rol === ROL_ADMIN) || DB.usuarios[0];
  DB.usuarios.forEach((u) => {
    u.propietaria = u === principal;
    if (u.propietaria) { u.rol = ROL_DUENA; u.estado = 'Activo'; }
    else if (u.rol === ROL_DUENA) u.rol = ROL_ADMIN;
  });

  if (!DB.usuarios.some((u) => u.usuario === 'rcampos')) {
    DB.usuarios.push({ usuario: 'rcampos', nombre: 'Rocío Campos', correo: 'rocio.campos@delitex.pe', rol: ROL_CONFECCION, estado: 'Activo', clave: 'delitex123', demo: true });
  }

  DB.usuarios.forEach((u) => {
    if (!u.id) u.id = siguienteIdUsuario();
    if (u.clave) establecerClave(u, u.clave);
    else if (!u.claveHash) { u.estado = 'Inactivo'; establecerClave(u, textoAleatorio()); }
    sellarUsuario(u);
  });

  // órdenes de trabajo ya generadas: se reparten entre las confeccionistas activas
  const taller = DB.usuarios.filter((u) => u.rol === ROL_CONFECCION && u.estado === 'Activo');
  let k = 0;
  DB.pedidos.forEach((p) => {
    if (p.ot && !p.asignadoA && taller.length) p.asignadoA = taller[k++ % taller.length].usuario;
  });

  if (!DB.config.seguridad) cambiarAdminGestionaUsuarios(true);
  DB.seguridad = VERSION_SEGURIDAD;
  guardarDatos();
}

/* ---------- Permisos ---------- */
function permisosDe(u) {
  return (u && usuarioIntegro(u) && PERMISOS[u.rol]) || null;
}

function puede(accion, u = usuarioSesion()) {
  const p = permisosDe(u);
  if (!p) return false;
  return p.acciones.includes('*') || p.acciones.includes(accion);
}

function puedeVerModulo(modulo, u = usuarioSesion()) {
  const p = permisosDe(u);
  if (!p || !p.modulos.includes(modulo)) return false;
  if (modulo === 'usuarios' && u.rol === ROL_ADMIN) return adminGestionaUsuarios();
  return true;
}

function paginaInicio(u = usuarioSesion()) {
  const p = permisosDe(u);
  return p ? p.inicio : 'index.html';
}

function nombreTablero(u = usuarioSesion()) {
  const p = permisosDe(u);
  return p ? p.tablero : '';
}

function rolesAsignables(u = usuarioSesion()) {
  const p = permisosDe(u);
  return p && puedeVerModulo('usuarios', u) ? p.rolesAsignables : [];
}

// ¿La persona "actor" puede modificar la cuenta "objetivo"?
function puedeGestionarUsuario(objetivo, actor = usuarioSesion()) {
  if (!objetivo || !puede('usuarios.gestionar', actor) || !puedeVerModulo('usuarios', actor)) return false;
  if (objetivo.propietaria) return actor.usuario === objetivo.usuario;   // la cuenta principal solo la toca la dueña
  if (actor.rol === ROL_DUENA) return true;
  return objetivo.usuario !== actor.usuario && rolesAsignables(actor).includes(objetivo.rol);
}

function puedeEliminarUsuario(objetivo, actor = usuarioSesion()) {
  return puedeGestionarUsuario(objetivo, actor) && !objetivo.propietaria && objetivo.usuario !== actor.usuario;
}

// Órdenes de trabajo: la confeccionista solo ve y avanza las que tiene asignadas
function ordenVisible(p, u = usuarioSesion()) {
  if (puede('produccion.verTodas', u)) return true;
  return !!u && !!p.ot && p.asignadoA === u.usuario;
}

function pedidoVisible(p, u = usuarioSesion()) {
  if (puede('seguimiento.verTodos', u)) return true;
  return !!u && p.asignadoA === u.usuario;
}

function puedeAvanzarOrden(p, u = usuarioSesion()) {
  if (p.estado !== 'En producción') return false;
  if (puede('produccion.avanzar', u)) return true;
  return puede('produccion.avanzarPropias', u) && p.asignadoA === u.usuario;
}

/* ---------- Sesión ----------
   El navegador solo guarda un token aleatorio. El rol NUNCA se guarda en la
   sesión: se lee de la cuenta (sellada) en cada página. */
let USUARIO_SESION = null;

function usuarioSesion() {
  return USUARIO_SESION;
}

function leerSesion() {
  return leerJSON(CLAVE_SESION, null);
}

function abrirSesion(u) {
  const token = textoAleatorio(32);
  const id = sha256(token);
  const ahora = Date.now();
  const sesiones = leerJSON(CLAVE_SESIONES, {});
  sesiones[id] = { usuario: u.usuario, creada: ahora, ultimoAcceso: ahora };
  sesiones[id].sello = firmar(['sesion', id, u.usuario, ahora].join('|'));
  guardarJSON(CLAVE_SESIONES, sesiones);
  guardarJSON(CLAVE_SESION, { token });
  USUARIO_SESION = u;
}

function cerrarSesion() {
  const s = leerSesion();
  if (s && s.token) {
    const sesiones = leerJSON(CLAVE_SESIONES, {});
    delete sesiones[sha256(s.token)];
    guardarJSON(CLAVE_SESIONES, sesiones);
  }
  try { localStorage.removeItem(CLAVE_SESION); } catch (e) {}
  USUARIO_SESION = null;
}

// Invalida todas las sesiones de una cuenta (cambio de rol, estado o contraseña)
function cerrarSesionesDe(usuario, conservarActual = false) {
  const s = leerSesion();
  const actual = s && s.token ? sha256(s.token) : null;
  const sesiones = leerJSON(CLAVE_SESIONES, {});
  Object.keys(sesiones).forEach((id) => {
    if (sesiones[id].usuario === usuario && !(conservarActual && id === actual)) delete sesiones[id];
  });
  guardarJSON(CLAVE_SESIONES, sesiones);
}

function validarSesion() {
  const s = leerSesion();
  if (!s || !s.token) return { usuario: null, motivo: '' };
  const id = sha256(s.token);
  const sesiones = leerJSON(CLAVE_SESIONES, {});
  const r = sesiones[id];
  const fallo = (motivo) => {
    cerrarSesion();
    return { usuario: null, motivo };
  };
  if (!r || r.sello !== firmar(['sesion', id, r.usuario, r.creada].join('|'))) return fallo('sesion');
  if (Date.now() - r.ultimoAcceso > HORAS_SESION * 3600 * 1000) return fallo('expirada');
  const u = DB.usuarios.find((x) => x.usuario === r.usuario);
  if (!u) return fallo('sesion');
  if (!usuarioIntegro(u)) return fallo('integridad');
  if (u.estado !== 'Activo') return fallo('inactivo');
  r.ultimoAcceso = Date.now();
  guardarJSON(CLAVE_SESIONES, sesiones);
  return { usuario: u, motivo: '' };
}

function iniciarSesion(identificador, clave, rolEsperado) {
  const valor = String(identificador || '').trim().toLowerCase();
  const intentos = leerJSON(CLAVE_INTENTOS, {});
  const registro = intentos[valor] || { n: 0, hasta: 0 };
  if (registro.hasta > Date.now()) {
    const min = Math.ceil((registro.hasta - Date.now()) / 60000);
    return { ok: false, error: `Demasiados intentos fallidos. Intenta de nuevo en ${min} minuto(s).` };
  }
  const u = DB.usuarios.find((x) => x.usuario.toLowerCase() === valor || x.correo.toLowerCase() === valor);
  if (!u || !verificarClave(u, clave)) {
    registro.n += 1;
    if (registro.n >= MAX_INTENTOS) { registro.n = 0; registro.hasta = Date.now() + MINUTOS_BLOQUEO * 60000; }
    intentos[valor] = registro;
    guardarJSON(CLAVE_INTENTOS, intentos);
    return { ok: false, error: 'Usuario o contraseña incorrectos.' };
  }
  delete intentos[valor];
  guardarJSON(CLAVE_INTENTOS, intentos);
  if (!usuarioIntegro(u)) {
    return { ok: false, error: 'La cuenta fue modificada fuera del sistema y está bloqueada. Comunícate con la dueña.' };
  }
  if (u.estado !== 'Activo') {
    return { ok: false, error: 'Tu usuario está inactivo. Comunícate con la dueña o la administradora.' };
  }
  if (rolEsperado && u.rol !== rolEsperado) {
    return { ok: false, error: `Esta cuenta no pertenece al perfil ${rolEsperado}. Selecciona el perfil correcto.` };
  }
  cerrarSesion();
  abrirSesion(u);
  return { ok: true, usuario: u, destino: paginaInicio(u) };
}

/* ---------- Inicio de sesión con selección de perfil ----------
   Valida el perfil elegido y las credenciales. Con los accesos de prueba del perfil
   abre la cuenta asociada; cualquier otra cuenta se valida como siempre, pero además
   debe tener el rol del perfil elegido. */
function sinTildes(texto) {
  return String(texto || '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

function iniciarSesionPerfil(rol, identificador, clave) {
  const perfil = PERFILES_PRUEBA[rol];
  if (!perfil) return { ok: false, error: 'Selecciona un perfil (Dueña, Administradora o Confeccionista) para ingresar.' };

  if (sinTildes(identificador) !== sinTildes(perfil.usuario) || clave !== perfil.clave) {
    return iniciarSesion(identificador, clave, rol);   // cuentas registradas
  }

  const clavePrueba = 'prueba:' + perfil.usuario;
  const intentos = leerJSON(CLAVE_INTENTOS, {});
  const registro = intentos[clavePrueba] || { n: 0, hasta: 0 };
  if (registro.hasta > Date.now()) {
    const min = Math.ceil((registro.hasta - Date.now()) / 60000);
    return { ok: false, error: `Demasiados intentos fallidos. Intenta de nuevo en ${min} minuto(s).` };
  }
  const u = DB.usuarios.find((x) => x.usuario === perfil.cuenta);
  if (!u || u.rol !== rol) return { ok: false, error: 'La cuenta de prueba de este perfil no está disponible.' };
  if (!usuarioIntegro(u)) {
    return { ok: false, error: 'La cuenta fue modificada fuera del sistema y está bloqueada. Comunícate con la dueña.' };
  }
  if (u.estado !== 'Activo') {
    return { ok: false, error: 'Tu usuario está inactivo. Comunícate con la dueña o la administradora.' };
  }
  delete intentos[clavePrueba];
  guardarJSON(CLAVE_INTENTOS, intentos);
  cerrarSesion();
  abrirSesion(u);
  return { ok: true, usuario: u, destino: paginaInicio(u) };
}

/* ---------- Recuperación de contraseña con enlace de un solo uso ---------- */
function crearEnlaceRecuperacion(correo) {
  const u = DB.usuarios.find((x) => x.correo.toLowerCase() === String(correo).trim().toLowerCase());
  if (!u || u.estado !== 'Activo' || !usuarioIntegro(u)) return null;
  const token = textoAleatorio(24);
  const pendientes = leerJSON(CLAVE_RECUPERACION, {});
  pendientes[sha256(token)] = { usuario: u.usuario, expira: Date.now() + 15 * 60000 };
  guardarJSON(CLAVE_RECUPERACION, pendientes);
  // Sin servidor de correo el enlace se muestra en pantalla, pero solo para cuentas sin
  // privilegios: así nadie puede apoderarse de la cuenta de la dueña o de la administradora.
  return { usuario: u, enlace: u.rol === ROL_CONFECCION ? 'restablecer.html?token=' + token : null };
}

function usuarioDeRecuperacion(token) {
  if (!token) return null;
  const r = leerJSON(CLAVE_RECUPERACION, {})[sha256(token)];
  if (!r || r.expira < Date.now()) return null;
  return DB.usuarios.find((u) => u.usuario === r.usuario) || null;
}

function usarEnlaceRecuperacion(token, clave) {
  const u = usuarioDeRecuperacion(token);
  if (!u) return false;
  const pendientes = leerJSON(CLAVE_RECUPERACION, {});
  delete pendientes[sha256(token)];
  guardarJSON(CLAVE_RECUPERACION, pendientes);
  establecerClave(u, clave);
  guardarDatos();
  cerrarSesionesDe(u.usuario);
  return true;
}

/* ---------- Guardia de páginas ---------- */
const ACCESO = { pagina: '', permitido: true, redirigiendo: false };

function paginaDeUrl(href) {
  try {
    const url = new URL(href, location.href);
    if (url.protocol !== location.protocol || url.host !== location.host) return null;
    const archivo = url.pathname.split('/').pop() || 'index.html';
    return archivo.replace(/\.html?$/i, '');
  } catch (e) {
    return null;
  }
}

function redirigirLogin(motivo) {
  ACCESO.permitido = false;
  ACCESO.redirigiendo = true;
  location.replace('index.html' + (motivo ? '?motivo=' + motivo : ''));
}

function ocultarHastaVerificar() {
  const estilo = document.createElement('style');
  estilo.id = 'estiloVerificando';
  estilo.textContent = 'body{visibility:hidden!important}';
  document.head.appendChild(estilo);
}

function revelarPagina() {
  const estilo = document.getElementById('estiloVerificando');
  if (estilo) estilo.remove();
}

function protegerPagina() {
  asegurarCuentas();
  const pagina = paginaDeUrl(location.href) || 'index';
  const { usuario, motivo } = validarSesion();
  USUARIO_SESION = usuario;
  ACCESO.pagina = pagina;

  if (PAGINAS_PUBLICAS.includes(pagina)) {
    // con sesión abierta no tiene sentido volver al inicio de sesión
    if (usuario && pagina === 'index') {
      ACCESO.redirigiendo = true;
      location.replace(paginaInicio(usuario));
    }
    return;
  }
  ocultarHastaVerificar();
  if (!usuario) {
    redirigirLogin(motivo || 'sinsesion');
    return;
  }
  ACCESO.permitido = !MODULOS[pagina] || puedeVerModulo(pagina, usuario);
}

/* ---------- Interfaz según el rol ---------- */
function marcarSinPermiso(el) {
  el.classList.add('d-none');
  el.setAttribute('data-sin-permiso', '');
  el.setAttribute('aria-hidden', 'true');
}

// Oculta enlaces a módulos no permitidos y elementos [data-permiso] sin permiso
function aplicarPermisos(raiz) {
  if (!raiz || !raiz.querySelectorAll || !usuarioSesion()) return;
  const enlaces = raiz.matches && raiz.matches('a[href]') ? [raiz] : [];
  enlaces.push(...raiz.querySelectorAll('a[href]'));
  enlaces.forEach((a) => {
    if (a.hasAttribute('data-sin-permiso')) return;
    const destino = paginaDeUrl(a.getAttribute('href'));
    if (!destino || !MODULOS[destino] || puedeVerModulo(destino)) return;
    if (destino === 'dashboard') a.setAttribute('href', paginaInicio());   // "Inicio" / "Volver" llevan a su página inicial
    else marcarSinPermiso(a);
  });
  const conPermiso = raiz.matches && raiz.matches('[data-permiso]') ? [raiz] : [];
  conPermiso.push(...raiz.querySelectorAll('[data-permiso]'));
  conPermiso.forEach((el) => { if (!puede(el.dataset.permiso)) marcarSinPermiso(el); });
}

function adaptarMenu() {
  const nav = document.querySelector('.sidebar-nav');
  if (!nav) return;
  let seccion = null;
  let visibles = 0;
  const cerrarSeccion = () => { if (seccion) seccion.classList.toggle('d-none', !visibles); };
  [...nav.children].forEach((el) => {
    if (el.classList.contains('nav-section')) {
      cerrarSeccion();
      seccion = el;
      visibles = 0;
    } else if (el.matches('a.sidebar-link')) {
      const destino = paginaDeUrl(el.getAttribute('href'));
      if (destino && MODULOS[destino] && !puedeVerModulo(destino)) marcarSinPermiso(el);
      else visibles++;
    }
  });
  cerrarSeccion();
  const marca = document.querySelector('.sidebar-brand');
  if (marca) marca.setAttribute('href', paginaInicio());

  // "Mi perfil" y "Cambiar contraseña" sin acceso a Configuración: se abren en una ventana propia
  if (!puedeVerModulo('configuracion')) {
    document.querySelectorAll('.sidebar-user a[href^="configuracion.html"]').forEach((a) => {
      const hash = a.getAttribute('href').split('#')[1];
      if (hash === 'cuenta' || hash === 'clave') {
        a.setAttribute('href', '#');
        a.setAttribute('data-mi-cuenta', hash);
      } else {
        a.closest('li').remove();
      }
    });
  }
}

function mostrarAccesoDenegado(pagina) {
  const u = usuarioSesion();
  const contenido = document.querySelector('.app-content');
  document.querySelectorAll('.modal').forEach((m) => m.remove());
  const titulo = document.querySelector('.topbar-title');
  if (titulo) titulo.textContent = 'Acceso restringido';
  document.title = 'Acceso restringido | DELITEX Confecciones';
  if (!contenido) return;
  const texto = (s) => { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; };
  contenido.innerHTML = `
    <div class="page-header">
      <div>
        <h2>Acceso restringido</h2>
        <p>Esta sección no está disponible para tu rol.</p>
      </div>
    </div>
    <div class="card">
      <div class="empty-state" role="alert">
        <i class="bi bi-shield-lock"></i>
        <strong class="d-block fs-5 text-body mb-2">No tienes permisos para acceder a este módulo</strong>
        <span class="d-block mb-3">Tu rol <strong>${texto(u.rol)}</strong> no tiene acceso a <strong>${texto(MODULOS[pagina] || pagina)}</strong>.</span>
        <a class="btn btn-primary" href="${paginaInicio(u)}">Ir a mi página de inicio</a>
      </div>
    </div>`;
}

function iniciarControlDeAcceso() {
  const pagina = document.body.dataset.page || ACCESO.pagina;
  if (ACCESO.redirigiendo) return;
  if (PAGINAS_PUBLICAS.includes(pagina)) return;
  try {
    if (!usuarioSesion()) {
      redirigirLogin('sinsesion');
      return;
    }
    // data-page es la referencia definitiva del módulo de la página
    ACCESO.pagina = pagina;
    ACCESO.permitido = !MODULOS[pagina] || puedeVerModulo(pagina);
    adaptarMenu();
    if (!ACCESO.permitido) mostrarAccesoDenegado(pagina);
    aplicarPermisos(document.body);
    // lo que las pantallas pintan después (tablas, avisos) también se revisa
    new MutationObserver((cambios) => {
      cambios.forEach((c) => c.addedNodes.forEach((n) => { if (n.nodeType === 1) aplicarPermisos(n); }));
    }).observe(document.body, { childList: true, subtree: true });
  } finally {
    if (!ACCESO.redirigiendo) revelarPagina();
  }
}

// Un enlace a un módulo sin permiso no navega (respaldo de lo anterior)
document.addEventListener('click', (e) => {
  const a = e.target.closest && e.target.closest('a[href]');
  if (!a || !usuarioSesion()) return;
  const destino = paginaDeUrl(a.getAttribute('href'));
  if (destino && MODULOS[destino] && !puedeVerModulo(destino)) {
    e.preventDefault();
    if (typeof mostrarToast === 'function') mostrarToast('No tienes permisos para acceder a este módulo.', 'error');
  }
}, true);

// Cerrar sesión (o entrar con otra cuenta) en una pestaña se refleja en todas
window.addEventListener('storage', (e) => {
  if (e.key !== CLAVE_SESION || PAGINAS_PUBLICAS.includes(ACCESO.pagina)) return;
  if (!e.newValue) redirigirLogin('cerrada');
  else location.reload();
});

// Al volver con el botón "Atrás" después de cerrar sesión, la página no se muestra desde la caché
window.addEventListener('pageshow', (e) => {
  if (e.persisted && !PAGINAS_PUBLICAS.includes(ACCESO.pagina) && !validarSesion().usuario) redirigirLogin('cerrada');
});

protegerPagina();
document.addEventListener('DOMContentLoaded', iniciarControlDeAcceso);
