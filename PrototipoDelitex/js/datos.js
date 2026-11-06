const CLAVE_DATOS = 'delitex-datos-v2';
const CLAVE_SESION = 'delitex-sesion';

const TALLAS = ['S', 'M', 'L', 'XL', 'XXL'];
const ETAPAS = ['Corte', 'Costura', 'Estampado', 'Acabado'];
const TIPOS_TELA = ['Dry-fit', 'Microfibra', 'Piqué'];
const METODOS_PAGO = ['Efectivo', 'Yape', 'Plin', 'Transferencia', 'Tarjeta'];
const ROLES = ['Administrador', 'Recepción', 'Taller', 'Almacén'];
const CATEGORIAS_INSUMO = ['Tela', 'Hilo', 'Accesorio', 'Estampado'];

function sumarDias(iso, dias) {
  const [a, m, d] = iso.split('-').map(Number);
  const f = new Date(a, m - 1, d + dias);
  return `${f.getFullYear()}-${String(f.getMonth() + 1).padStart(2, '0')}-${String(f.getDate()).padStart(2, '0')}`;
}

function datosIniciales() {
  const config = {
    empresa: {
      nombre: 'DELITEX Confecciones',
      ruc: '20601234567',
      direccion: 'Av. Los Deportistas 245, Ica',
      telefono: '956 123 456',
      correo: 'contacto@delitex.pe'
    },
    pedidos: { adelantoMinimo: 30, diasEntrega: 7 },
    inventario: { porcentajeCritico: 30 },
    prendas: {
      'Conjunto de fútbol': { tela: 'Dry-fit', precio: 90, metros: 1.6, elastico: 0.8, cierre: 0 },
      'Camiseta deportiva': { tela: 'Dry-fit', precio: 45, metros: 0.9, elastico: 0, cierre: 0 },
      'Short deportivo': { tela: 'Dry-fit', precio: 35, metros: 0.6, elastico: 0.8, cierre: 0 },
      'Buzo deportivo': { tela: 'Microfibra', precio: 85, metros: 2.2, elastico: 0.8, cierre: 1 },
      'Casaca cortaviento': { tela: 'Microfibra', precio: 120, metros: 1.8, elastico: 0, cierre: 1 },
      'Polo deportivo': { tela: 'Piqué', precio: 55, metros: 1.0, elastico: 0, cierre: 0 }
    },
    estampados: {
      'Sin estampado': 0,
      'Nombre y número en la espalda': 8,
      'Logo / escudo en el pecho': 6,
      'Sublimado completo': 15
    }
  };

  const insumos = [
    { codigo: 'INS-001', nombre: 'Tela dry-fit', color: 'Azul marino', categoria: 'Tela', unidad: 'm', stock: 12, minimo: 40 },
    { codigo: 'INS-002', nombre: 'Tela dry-fit', color: 'Blanco', categoria: 'Tela', unidad: 'm', stock: 85, minimo: 40 },
    { codigo: 'INS-003', nombre: 'Tela dry-fit', color: 'Amarillo', categoria: 'Tela', unidad: 'm', stock: 64, minimo: 30 },
    { codigo: 'INS-004', nombre: 'Tela dry-fit', color: 'Negro', categoria: 'Tela', unidad: 'm', stock: 52, minimo: 30 },
    { codigo: 'INS-005', nombre: 'Tela dry-fit', color: 'Rojo', categoria: 'Tela', unidad: 'm', stock: 46, minimo: 30 },
    { codigo: 'INS-006', nombre: 'Tela dry-fit', color: 'Verde', categoria: 'Tela', unidad: 'm', stock: 38, minimo: 30 },
    { codigo: 'INS-007', nombre: 'Tela microfibra', color: 'Verde', categoria: 'Tela', unidad: 'm', stock: 18, minimo: 30 },
    { codigo: 'INS-008', nombre: 'Tela microfibra', color: 'Azul', categoria: 'Tela', unidad: 'm', stock: 45, minimo: 30 },
    { codigo: 'INS-009', nombre: 'Tela microfibra', color: 'Rojo', categoria: 'Tela', unidad: 'm', stock: 41, minimo: 30 },
    { codigo: 'INS-010', nombre: 'Tela microfibra', color: 'Guinda', categoria: 'Tela', unidad: 'm', stock: 38, minimo: 30 },
    { codigo: 'INS-011', nombre: 'Tela piqué', color: 'Negro', categoria: 'Tela', unidad: 'm', stock: 33, minimo: 25 },
    { codigo: 'INS-012', nombre: 'Tela piqué', color: 'Blanco', categoria: 'Tela', unidad: 'm', stock: 40, minimo: 25 },
    { codigo: 'INS-013', nombre: 'Tela piqué', color: 'Celeste', categoria: 'Tela', unidad: 'm', stock: 28, minimo: 25 },
    { codigo: 'INS-014', nombre: 'Hilo poliéster', color: 'Blanco', categoria: 'Hilo', unidad: 'conos', stock: 4, minimo: 10 },
    { codigo: 'INS-015', nombre: 'Hilo poliéster', color: 'Negro', categoria: 'Hilo', unidad: 'conos', stock: 14, minimo: 10 },
    { codigo: 'INS-016', nombre: 'Elástico de 3 cm', color: '', categoria: 'Accesorio', unidad: 'm', stock: 6, minimo: 25 },
    { codigo: 'INS-017', nombre: 'Vinil textil', color: 'Dorado', categoria: 'Estampado', unidad: 'rollos', stock: 2, minimo: 5 },
    { codigo: 'INS-018', nombre: 'Vinil textil', color: 'Blanco', categoria: 'Estampado', unidad: 'rollos', stock: 9, minimo: 5 },
    { codigo: 'INS-019', nombre: 'Tinta de sublimación', color: '', categoria: 'Estampado', unidad: 'litros', stock: 6, minimo: 3 },
    { codigo: 'INS-020', nombre: 'Cierre de 50 cm', color: '', categoria: 'Accesorio', unidad: 'unid.', stock: 120, minimo: 50 }
  ];

  const clientes = [
    { dni: '41235678', nombre: 'Carlos Mendoza Ríos', institucion: 'Club Deportivo Los Halcones', telefono: '987 654 321', correo: 'cmendoza@gmail.com', direccion: 'Calle Lima 120, Ica', fecha: '2026-09-02' },
    { dni: '43567812', nombre: 'Rosa Huamán Díaz', institucion: 'I.E. San Martín de Porres', telefono: '956 778 120', correo: 'rhuaman@iesanmartin.edu.pe', direccion: 'Av. Grau 455, Ica', fecha: '2026-09-04' },
    { dni: '46789123', nombre: 'Jorge Paredes Salas', institucion: 'Academia de Vóley Las Águilas', telefono: '945 332 118', correo: 'jparedes@gmail.com', direccion: 'Jr. Ayacucho 310, Ica', fecha: '2026-09-05' },
    { dni: '40987654', nombre: 'Luis Cárdenas Vega', institucion: 'Gimnasio Fuerza Total', telefono: '912 456 789', correo: 'lcardenas@fuerzatotal.pe', direccion: 'Av. San Martín 890, Ica', fecha: '2026-09-07' },
    { dni: '42345671', nombre: 'Ana Torres Quispe', institucion: 'Liga Vecinal Santa Rosa', telefono: '934 221 008', correo: 'atorres@gmail.com', direccion: 'Mz. B Lt. 12 Santa Rosa, Ica', fecha: '2026-09-09' },
    { dni: '47891234', nombre: 'Pedro Ramírez Soto', institucion: 'Club Atlético Juventud Unida', telefono: '965 870 443', correo: 'pramirez@hotmail.com', direccion: 'Calle Bolívar 77, Ica', fecha: '2026-09-10' },
    { dni: '44561237', nombre: 'Lucía Flores Medina', institucion: 'Colegio Santa Ana', telefono: '978 120 334', correo: 'lflores@santaana.edu.pe', direccion: 'Av. Matías Manzanilla 560, Ica', fecha: '2026-09-11' },
    { dni: '45678912', nombre: 'Miguel Castillo Peña', institucion: 'Escuela de Fútbol Real Ica', telefono: '923 554 671', correo: 'mcastillo@realica.pe', direccion: 'Urb. San José D-4, Ica', fecha: '2026-09-12' },
    { dni: '41122334', nombre: 'Sofía Gutiérrez León', institucion: 'Academia Kids Sport', telefono: '951 640 227', correo: 'sgutierrez@kidssport.pe', direccion: 'Calle Callao 210, Ica', fecha: '2026-09-14' },
    { dni: '48765432', nombre: 'Raúl Navarro Chávez', institucion: 'Club de Básquet Titanes', telefono: '987 004 556', correo: 'rnavarro@gmail.com', direccion: 'Av. Cutervo 150, Ica', fecha: '2026-09-15' },
    { dni: '43344556', nombre: 'Elena Vargas Ruiz', institucion: 'I.E. Señor de Luren', telefono: '956 330 912', correo: 'evargas@luren.edu.pe', direccion: 'Av. Luren 600, Ica', fecha: '2026-09-16' },
    { dni: '49988776', nombre: 'Diego Salazar Muñoz', institucion: 'Empresa Agroexport Sur', telefono: '914 778 205', correo: 'dsalazar@agroexportsur.pe', direccion: 'Panamericana Sur km 300, Ica', fecha: '2026-09-17' }
  ];

  const base = [
    ['PED-0235', '47891234', '2026-09-18', 'Conjunto de fútbol', 'Dry-fit', 'Amarillo', [2, 5, 6, 4, 1], 'Nombre y número en la espalda', 'Entregado', null, 0.5, true],
    ['PED-0236', '41122334', '2026-09-20', 'Short deportivo', 'Dry-fit', 'Negro', [10, 14, 10, 6, 0], 'Sin estampado', 'Entregado', null, 0.5, true],
    ['PED-0237', '43344556', '2026-09-23', 'Buzo deportivo', 'Microfibra', 'Azul', [12, 18, 16, 10, 4], 'Logo / escudo en el pecho', 'Entregado', null, 0.4, true],
    ['PED-0238', '48765432', '2026-09-25', 'Camiseta deportiva', 'Dry-fit', 'Rojo', [0, 4, 6, 4, 1], 'Nombre y número en la espalda', 'Entregado', null, 0.5, true],
    ['PED-0239', '49988776', '2026-09-29', 'Polo deportivo', 'Piqué', 'Celeste', [8, 16, 14, 8, 4], 'Logo / escudo en el pecho', 'Entregado', null, 0.5, true],
    ['PED-0240', '42345671', '2026-10-01', 'Casaca cortaviento', 'Microfibra', 'Rojo', [2, 6, 7, 4, 1], 'Logo / escudo en el pecho', 'Entregado', null, 0.5, true],
    ['PED-0241', '47891234', '2026-10-02', 'Conjunto de fútbol', 'Dry-fit', 'Blanco', [2, 5, 6, 4, 1], 'Nombre y número en la espalda', 'Entregado', null, 0.5, true],
    ['PED-0242', '44561237', '2026-10-03', 'Buzo deportivo', 'Microfibra', 'Guinda', [8, 10, 9, 6, 2], 'Logo / escudo en el pecho', 'Listo para entrega', null, 0.5, true],
    ['PED-0243', '45678912', '2026-10-05', 'Conjunto de fútbol', 'Dry-fit', 'Verde', [4, 8, 7, 4, 1], 'Nombre y número en la espalda', 'En producción', 'Costura', 0.5, false],
    ['PED-0244', '42345671', '2026-10-06', 'Short deportivo', 'Dry-fit', 'Rojo', [3, 6, 6, 4, 1], 'Sin estampado', 'Entregado', null, 1, true],
    ['PED-0245', '40987654', '2026-10-07', 'Polo deportivo', 'Piqué', 'Negro', [4, 8, 10, 6, 2], 'Logo / escudo en el pecho', 'En producción', 'Costura', 0.5, false],
    ['PED-0246', '46789123', '2026-10-08', 'Camiseta deportiva', 'Dry-fit', 'Blanco', [2, 5, 5, 3, 1], 'Nombre y número en la espalda', 'Listo para entrega', null, 0.5, false],
    ['PED-0247', '43567812', '2026-10-10', 'Buzo deportivo', 'Microfibra', 'Verde', [10, 14, 12, 7, 2], 'Logo / escudo en el pecho', 'En producción', 'Corte', 0.3, false],
    ['PED-0248', '41235678', '2026-10-12', 'Conjunto de fútbol', 'Dry-fit', 'Azul marino', [3, 6, 7, 5, 1], 'Nombre y número en la espalda', 'En producción', 'Estampado', 0.5, false],
    ['PED-0249', '48765432', '2026-10-13', 'Camiseta deportiva', 'Dry-fit', 'Negro', [0, 4, 6, 4, 1], 'Nombre y número en la espalda', 'Registrado', null, 0.4, false],
    ['PED-0250', '41122334', '2026-10-14', 'Conjunto de fútbol', 'Dry-fit', 'Rojo', [6, 8, 6, 0, 0], 'Logo / escudo en el pecho', 'Registrado', null, 0, false]
  ];

  const detalles = {
    'Nombre y número en la espalda': 'Nombres y números según lista entregada por el cliente.',
    'Logo / escudo en el pecho': 'Escudo de la institución en el lado izquierdo del pecho.',
    'Sin estampado': ''
  };

  const corte = '2026-10-14';
  const tope = (iso) => (iso > corte ? corte : iso);
  let numOT = 120;
  let numRecibo = 300;
  const metodos = ['Efectivo', 'Yape', 'Transferencia', 'Plin'];
  const movimientos = [];

  const pedidos = base.map(([codigo, dni, fecha, prenda, tela, color, cant, estampado, estado, etapa, pctAdelanto, cancelado], i) => {
    const tallas = {};
    TALLAS.forEach((t, k) => { tallas[t] = cant[k]; });
    const cantidad = cant.reduce((a, b) => a + b, 0);
    const precioUnit = config.prendas[prenda].precio + config.estampados[estampado];
    const total = cantidad * precioUnit;
    const insumo = insumos.find((x) => x.categoria === 'Tela' && x.nombre === 'Tela ' + tela.toLowerCase() && x.color === color);
    const fechas = { registrado: fecha };
    const p = {
      codigo, dni, fecha, prenda, tela, color,
      telaCodigo: insumo ? insumo.codigo : '',
      tallas, cantidad, estampado,
      detalleEstampado: detalles[estampado] || '',
      observaciones: '',
      precioUnit, total,
      fechaEntrega: sumarDias(fecha, 10),
      estado, ot: null, fechas, pagos: []
    };

    if (pctAdelanto > 0) {
      const monto = pctAdelanto >= 1 ? total : Math.round(total * pctAdelanto);
      p.pagos.push({ fecha, monto, metodo: metodos[i % metodos.length], tipo: pctAdelanto >= 1 ? 'Cancelación' : 'Adelanto', recibo: 'R-' + String(++numRecibo).padStart(5, '0') });
    }

    if (estado !== 'Registrado') {
      p.ot = 'OT-' + String(++numOT).padStart(4, '0');
      fechas.ot = tope(sumarDias(fecha, 1));
      const etapas = ETAPAS.filter((e) => e !== 'Estampado' || estampado !== 'Sin estampado');
      const limite = estado === 'En producción' ? etapas.indexOf(etapa) : etapas.length - 1;
      etapas.slice(0, limite + 1).forEach((e, k) => { fechas[e.toLowerCase()] = tope(sumarDias(fecha, 1 + k * 2)); });
      if (estado === 'Listo para entrega' || estado === 'Entregado') fechas.listo = tope(sumarDias(fecha, 8));
      if (estado === 'Entregado') fechas.entregado = tope(sumarDias(fecha, 9));

      consumoDePedido(p, config, insumos).forEach((r) => {
        movimientos.push({ fecha: fechas.ot, tipo: 'Salida', codigo: r.codigo, cantidad: r.cantidad, referencia: p.ot, pedido: p.codigo });
      });
    }

    const pagado = p.pagos.reduce((a, x) => a + x.monto, 0);
    if (cancelado && pagado < total) {
      p.pagos.push({ fecha: fechas.listo || tope(sumarDias(fecha, 8)), monto: total - pagado, metodo: metodos[(i + 1) % metodos.length], tipo: 'Cancelación', recibo: 'R-' + String(++numRecibo).padStart(5, '0') });
    }
    return p;
  });

  movimientos.push(
    { fecha: '2026-09-15', tipo: 'Ingreso', codigo: 'INS-002', cantidad: 80, referencia: 'Compra a proveedor', pedido: '' },
    { fecha: '2026-09-21', tipo: 'Ingreso', codigo: 'INS-008', cantidad: 120, referencia: 'Compra a proveedor', pedido: '' },
    { fecha: '2026-10-04', tipo: 'Ingreso', codigo: 'INS-020', cantidad: 100, referencia: 'Compra a proveedor', pedido: '' }
  );

  const usuarios = [
    { usuario: 'mquispe', nombre: 'María Quispe', correo: 'maria.quispe@delitex.pe', rol: 'Administrador', estado: 'Activo', clave: 'delitex123' },
    { usuario: 'lrojas', nombre: 'Lucía Rojas', correo: 'lucia.rojas@delitex.pe', rol: 'Recepción', estado: 'Activo', clave: 'delitex123' },
    { usuario: 'jtaipe', nombre: 'Jorge Taipe', correo: 'jorge.taipe@delitex.pe', rol: 'Taller', estado: 'Activo', clave: 'delitex123' },
    { usuario: 'calva', nombre: 'Carmen Alva', correo: 'carmen.alva@delitex.pe', rol: 'Almacén', estado: 'Inactivo', clave: 'delitex123' }
  ];

  return { config, insumos, clientes, pedidos, movimientos, usuarios };
}

function redondear(n, d = 1) {
  const f = Math.pow(10, d);
  return Math.round(n * f) / f;
}

function consumoDePedido(p, config, insumos) {
  const cfg = config.prendas[p.prenda];
  if (!cfg) return [];
  const q = p.cantidad;
  const req = [];
  if (p.telaCodigo) req.push({ codigo: p.telaCodigo, cantidad: redondear(cfg.metros * q, 1) });
  const oscuro = ['negro', 'azul marino', 'guinda'].includes((p.color || '').toLowerCase());
  req.push({ codigo: oscuro ? 'INS-015' : 'INS-014', cantidad: Math.ceil(q / 10) });
  if (cfg.elastico) req.push({ codigo: 'INS-016', cantidad: redondear(cfg.elastico * q, 1) });
  if (cfg.cierre) req.push({ codigo: 'INS-020', cantidad: q * cfg.cierre });
  if (p.estampado === 'Nombre y número en la espalda' || p.estampado === 'Logo / escudo en el pecho') {
    req.push({ codigo: 'INS-018', cantidad: Math.ceil(q / 15) });
  }
  if (p.estampado === 'Sublimado completo') req.push({ codigo: 'INS-019', cantidad: redondear(q * 0.05, 2) });
  return req.filter((r) => r.cantidad > 0 && insumos.some((i) => i.codigo === r.codigo));
}

function cargarDatos() {
  try {
    const guardado = localStorage.getItem(CLAVE_DATOS);
    if (guardado) return JSON.parse(guardado);
  } catch (e) {}
  return datosIniciales();
}

const DB = cargarDatos();

function guardarDatos() {
  try {
    localStorage.setItem(CLAVE_DATOS, JSON.stringify(DB));
  } catch (e) {}
}

function restablecerDatos() {
  try {
    localStorage.removeItem(CLAVE_DATOS);
  } catch (e) {}
}

function leerSesion() {
  try {
    return JSON.parse(localStorage.getItem(CLAVE_SESION));
  } catch (e) {
    return null;
  }
}

function guardarSesion(usuario) {
  try {
    localStorage.setItem(CLAVE_SESION, JSON.stringify({ usuario: usuario.usuario }));
  } catch (e) {}
}

function cerrarSesion() {
  try {
    localStorage.removeItem(CLAVE_SESION);
  } catch (e) {}
}

guardarDatos();
