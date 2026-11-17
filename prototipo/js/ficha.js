/* ---------- Ficha de producción ----------
   Se genera desde Nuevo pedido (antes o después de registrar), desde la lista de Pedidos
   y desde Producción. Se puede imprimir o descargar en PDF (html2pdf.js, se carga solo al usarlo). */

const HTML2PDF_URL = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';

const LOGO_FICHA = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160" width="56" height="56" aria-hidden="true"> <defs> <linearGradient id="fpAro" x1="0" y1="0" x2="1" y2="1"> <stop offset="0" stop-color="#9BBDE0"/> <stop offset=".5" stop-color="#E8E0F4"/> <stop offset="1" stop-color="#F3DDE1"/> </linearGradient> <linearGradient id="fpHilo" x1="0" y1="0" x2="1" y2="1"> <stop offset="0" stop-color="#E2BC74"/> <stop offset="1" stop-color="#D99AAB"/> </linearGradient> <linearGradient id="fpPolo" x1="0" y1="0" x2="0" y2="1"> <stop offset="0" stop-color="#B5CFEA"/> <stop offset="1" stop-color="#9BBDE0"/> </linearGradient> </defs> <circle cx="80" cy="80" r="78" fill="url(#fpAro)"/> <circle cx="80" cy="80" r="66" fill="#FFFDF9"/> <circle cx="80" cy="80" r="60" fill="none" stroke="#C9D9EC" stroke-width="1.6" stroke-dasharray="3 4"/> <path d="M80 98 L80 118" stroke="#526681" stroke-width="3.2" stroke-linecap="round"/> <path d="M65 124 Q80 116 95 124" fill="none" stroke="#526681" stroke-width="3.2" stroke-linecap="round"/> <rect x="75" y="28" width="10" height="9" rx="3.5" fill="#526681"/> <path d="M64 40 L72 37 Q80 46 88 37 L96 40 L111 48 L105 63 L98 60 L98 96 Q80 102 62 96 L62 60 L55 63 L49 48 Z" fill="url(#fpPolo)" stroke="#2B3A52" stroke-width="2.4" stroke-linejoin="round"/> <path d="M62 70 L98 70 L98 77 L62 77 Z" fill="#E8E0F4"/> <path d="M62 77 L98 77 L98 80 L62 80 Z" fill="#F3DDE1"/> <path d="M86 50 h8 v5 q0 4 -4 6 q-4 -2 -4 -6 Z" fill="#F7E9BD" stroke="#2B3A52" stroke-width="1.4" stroke-linejoin="round"/> <path d="M72 37 Q80 46 88 37" fill="none" stroke="#FFFFFF" stroke-width="1.6" opacity=".9"/> <path d="M114 31 C141 60 132 110 97 117 C66 123 37 109 35 84 C33 66 45 54 54 57" fill="none" stroke="url(#fpHilo)" stroke-width="2.4" stroke-linecap="round"/> <path d="M112 27 L128 53" stroke="#2B3A52" stroke-width="3" stroke-linecap="round"/> <ellipse cx="114.2" cy="30.5" rx="1.3" ry="2.6" transform="rotate(-32 114.2 30.5)" fill="#FFFDF9"/> <path d="M38 46 l2.2 5 5 2.2 -5 2.2 -2.2 5 -2.2 -5 -5 -2.2 5 -2.2 Z" fill="#E2BC74"/> <path d="M120 100 l1.6 3.6 3.6 1.6 -3.6 1.6 -1.6 3.6 -1.6 -3.6 -3.6 -1.6 3.6 -1.6 Z" fill="#B9A4DA"/> </svg>`;

const COLORES_TELA = {
  'azul marino': '#22385E',
  'blanco': '#FFFFFF',
  'amarillo': '#F2C94C',
  'negro': '#2A2D34',
  'rojo': '#D2474B',
  'verde': '#3F9A62',
  'azul': '#3D6FC4',
  'guinda': '#7B2335',
  'celeste': '#8EC9EE',
  'gris': '#9AA3AE',
  'naranja': '#F08A3C',
  'morado': '#7D55B5',
  'rosado': '#F2A7C0'
};

function colorTela(nombre) {
  return COLORES_TELA[(nombre || '').trim().toLowerCase()] || '#C9D3E0';
}

function esColorOscuro(hex) {
  const n = parseInt(hex.slice(1), 16);
  const r = (n >> 16) & 255;
  const g = (n >> 8) & 255;
  const b = n & 255;
  return (0.299 * r + 0.587 * g + 0.114 * b) < 140;
}

/* ---------- Dibujo técnico (vista frontal) de cada prenda ---------- */
let contadorDibujo = 0;

function dibujoPrenda(it) {
  const id = 'fpClip' + (++contadorDibujo);
  const relleno = colorTela(it.color);
  const linea = esColorOscuro(relleno) ? 'rgba(255,255,255,.75)' : 'rgba(43,58,82,.55)';
  const trazo = 'stroke="#2B3A52" stroke-width="2" stroke-linejoin="round"';
  const detalle = `fill="none" stroke="${linea}" stroke-width="1.4"`;

  const camiseta = 'M52 18 L68 12 Q80 24 92 12 L108 18 L134 40 L121 58 L108 50 L108 112 L52 112 L52 50 L39 58 L26 40 Z';
  const casaca = 'M52 18 L68 10 Q80 16 92 10 L108 18 L128 34 L142 102 L127 106 L110 54 L110 116 L50 116 L50 54 L33 106 L18 102 L32 34 Z';
  const short = (y) => `M50 ${y} L110 ${y} L116 ${y + 48} L86 ${y + 48} L80 ${y + 20} L74 ${y + 48} L44 ${y + 48} Z`;
  const pantalon = (y) => `M52 ${y} L108 ${y} L114 ${y + 76} L90 ${y + 76} L80 ${y + 22} L70 ${y + 76} L46 ${y + 76} Z`;

  const marcas = (forma) => {
    let m = '';
    if (it.estampado === 'Logo / escudo en el pecho') {
      m += '<path d="M88 30 h11 v6 q0 5 -5.5 7.5 q-5.5 -2.5 -5.5 -7.5 Z" fill="#F7E9BD" stroke="#2B3A52" stroke-width="1.2"/>';
    }
    if (it.estampado === 'Sublimado completo') {
      m += `<clipPath id="${id}"><path d="${forma}"/></clipPath>
        <g clip-path="url(#${id})" stroke="#FFFFFF" stroke-opacity=".45" stroke-width="7">
          <path d="M10 70 L90 -10 M10 110 L130 -10 M30 130 L150 10 M70 130 L150 50"/>
        </g>`;
    }
    if (it.estampado === 'Nombre y número en la espalda') {
      m += '<text x="80" y="84" text-anchor="middle" font-size="9" font-weight="800" fill="' + linea + '">N° atrás</text>';
    }
    return m;
  };

  const parteSuperior = (forma, extra) => `
    <path d="${forma}" fill="${relleno}" ${trazo}/>
    ${extra}
    ${marcas(forma)}`;

  const cuelloV = `<path d="M68 12 Q80 24 92 12" ${detalle}/>`;
  const cuelloPolo = `<path d="M68 12 L72 22 L80 28 L88 22 L92 12" ${detalle}/><path d="M80 28 L80 44" ${detalle}/>
    <circle cx="80" cy="34" r="1.6" fill="${linea}"/><circle cx="80" cy="40" r="1.6" fill="${linea}"/>`;
  const detallesCasaca = `<path d="M68 10 L68 4 L92 4 L92 10" fill="${relleno}" stroke="#2B3A52" stroke-width="2" stroke-linejoin="round"/>
    <path d="M80 6 L80 116" ${detalle} stroke-dasharray="3 2.5"/><path d="M50 108 L110 108" ${detalle}/>
    <path d="M20 96 L34 100 M140 96 L126 100" ${detalle}/>`;
  const detallesShort = (y) => `<path d="M50 ${y + 6} L110 ${y + 6}" ${detalle}/>`;
  const detallesPantalon = (y) => `<path d="M52 ${y + 6} L108 ${y + 6}" ${detalle}/><path d="M48 ${y + 68} L68 ${y + 68} M92 ${y + 68} L112 ${y + 68}" ${detalle}/>`;

  let cuerpo;
  switch (it.prenda) {
    case 'Conjunto de fútbol':
      cuerpo = `<g transform="translate(0 4)">${parteSuperior(camiseta, cuelloV)}</g>
        <path d="${short(122)}" fill="${relleno}" ${trazo}/>${detallesShort(122)}`;
      break;
    case 'Short deportivo':
      cuerpo = `<path d="${short(64)}" fill="${relleno}" ${trazo}/>${detallesShort(64)}`;
      break;
    case 'Buzo deportivo':
      cuerpo = `<g transform="translate(16 2) scale(.8)">${parteSuperior(casaca, detallesCasaca)}</g>
        <path d="${pantalon(102)}" fill="${relleno}" ${trazo}/>${detallesPantalon(102)}`;
      break;
    case 'Casaca cortaviento':
      cuerpo = `<g transform="translate(0 38)">${parteSuperior(casaca, detallesCasaca)}</g>`;
      break;
    case 'Polo deportivo':
      cuerpo = `<g transform="translate(0 40)">${parteSuperior(camiseta, cuelloPolo)}</g>`;
      break;
    default:
      cuerpo = `<g transform="translate(0 40)">${parteSuperior(camiseta, cuelloV)}</g>`;
  }
  return `<svg viewBox="0 0 160 200" width="136" height="170" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <ellipse cx="80" cy="190" rx="52" ry="5" fill="#2B3A52" opacity=".08"/>${cuerpo}</svg>`;
}

/* ---------- Contenido de la ficha ---------- */
function htmlFicha(p) {
  const c = p.cliente || clienteDe(p);
  const items = itemsDe(p);
  const borrador = !p.codigo;
  const codigo = p.codigo || siguienteCodigo('PED-', DB.pedidos.map((x) => x.codigo));
  const empresa = DB.config.empresa;
  const u = usuarioActual();
  const ahora = new Date();
  const generado = `${fechaCorta(hoyISO())} ${String(ahora.getHours()).padStart(2, '0')}:${String(ahora.getMinutes()).padStart(2, '0')}`;
  const conEstampado = items.some((it) => it.estampado !== 'Sin estampado');

  const dato = (etiqueta, valor) => `<div class="fp-dato"><span>${etiqueta}</span><strong>${valor}</strong></div>`;

  const dibujos = items.map((it, k) => `
    <div class="fp-dibujo">
      ${dibujoPrenda(it)}
      <strong>${esc(it.prenda)}</strong>
      <small>Prenda ${k + 1} · ${esc(it.color || '—')} · ${esc(it.estampado === 'Sin estampado' ? 'sin estampado' : it.estampado.toLowerCase())}</small>
    </div>`).join('');

  const prendas = items.map((it, k) => {
    const cfg = DB.config.prendas[it.prenda] || {};
    const q = it.cantidad;
    const metros = redondear((cfg.metros || 0) * q, 1);
    const elastico = redondear((cfg.elastico || 0) * q, 1);
    const cierres = (cfg.cierre || 0) * q;
    const hilo = ['negro', 'azul marino', 'guinda'].includes((it.color || '').toLowerCase()) ? 'negro' : 'blanco';
    const usadas = TALLAS.filter((t) => it.tallas[t] > 0);
    const reparto = usadas.map((t) => `<strong>${it.tallas[t]}</strong> en talla <strong>${t}</strong>`).join(', ');
    const sinEstampado = it.estampado === 'Sin estampado';
    const material = it.estampado === 'Sublimado completo' ? 'tinta de sublimación' : 'vinil textil blanco';

    const costura = [`Unir piezas con hilo poliéster ${hilo}.`];
    if (elastico) costura.push(`Pretina con elástico de 3 cm (${elastico} m en total).`);
    if (cierres) costura.push(`Colocar ${cierres} cierre(s) de 50 cm.`);

    return `
    <section class="fp-bloque">
      <h2>Prenda ${k + 1} · ${esc(it.prenda)} <small>${q} unidad${q === 1 ? '' : 'es'}</small></h2>
      <div class="fp-prenda-cuerpo">
        <table class="fp-tabla">
          <tr><td>Tipo de tela</td><td>${esc(it.tela)}</td></tr>
          <tr><td>Color</td><td><span class="fp-color" style="background:${colorTela(it.color)}"></span>${esc(it.color || '—')}</td></tr>
          <tr><td>Tela necesaria</td><td>${metros} m</td></tr>
          <tr><td>Estampado</td><td>${esc(it.estampado)}</td></tr>
          ${sinEstampado ? '' : `<tr><td>Detalle</td><td>${esc(it.detalleEstampado || '—')}</td></tr>`}
          <tr><td>Observaciones</td><td>${esc(it.observaciones || '—')}</td></tr>
        </table>
        <div class="fp-tallas">
          ${TALLAS.map((t) => `<div class="${it.tallas[t] > 0 ? '' : 'cero'}"><span>${t}</span><b>${it.tallas[t] || 0}</b></div>`).join('')}
          <div class="total"><span>Total</span><b>${q}</b></div>
          <div class="fp-metros">Hilo poliéster ${hilo}${elastico ? ` · Elástico ${elastico} m` : ''}${cierres ? ` · ${cierres} cierre(s)` : ''}</div>
        </div>
      </div>
      <ol class="fp-pasos">
        <li><span class="n">1</span><div><span class="t">Corte</span>Cortar ${q} ${q === 1 ? 'unidad' : 'unidades'} de ${esc(it.prenda.toLowerCase())} en ${esc((it.tela || '').toLowerCase())} ${esc((it.color || '').toLowerCase())}: ${reparto}. Tela: ${metros} m.</div></li>
        <li><span class="n">2</span><div><span class="t">Costura</span>${costura.join(' ')}</div></li>
        <li class="${sinEstampado ? 'omitido' : ''}"><span class="n">3</span><div><span class="t">Estampado</span>${sinEstampado ? 'Sin estampado: se omite este paso.' : `${esc(it.estampado)}${it.detalleEstampado ? ` — ${esc(it.detalleEstampado)}` : ''}. Material: ${material}.`}</div></li>
        <li><span class="n">4</span><div><span class="t">Acabado</span>Cortar hilos, revisar medidas, planchar y embolsar separando por talla.</div></li>
      </ol>
    </section>`;
  }).join('');

  const insumos = consumoPedido(p).map((r) => {
    const i = buscarInsumo(r.codigo);
    return `<tr><td>${esc(nombreInsumo(i))}</td><td>${r.cantidad} ${esc(i.unidad)}</td></tr>`;
  }).join('');

  const cuidados = [
    ...items.filter((it) => it.observaciones).map((it) => `Prenda ${items.indexOf(it) + 1}: ${esc(it.observaciones)}`),
    ...(conEstampado ? ['Estampado centrado y simétrico; revisar nombres y números con la lista del cliente'] : []),
    'Verificar que el color de la tela coincida con el pedido',
    'Costuras alineadas y sin hilos sueltos',
    'Separar y etiquetar las prendas por talla'
  ];

  const criterios = ['Medidas', 'Costuras', ...(conEstampado ? ['Estampado'] : []), 'Acabado', 'Cantidad por talla'];

  return `<div class="ficha-prod">
    <header class="fp-top">
      <span class="fp-logo">${LOGO_FICHA}</span>
      <div>
        <h1>FICHA DE PRODUCCIÓN</h1>
        <p>Instrucciones del pedido para el taller</p>
      </div>
      <div class="fp-empresa"><strong>DELITEX</strong>Confecciones deportivas<br>${esc(empresa.telefono)} · RUC ${esc(empresa.ruc)}</div>
    </header>
    ${borrador ? '<p class="fp-aviso"><strong>Vista previa:</strong> el código del pedido se confirma al registrarlo y la orden de trabajo (OT) se asigna en Producción.</p>' : ''}
    <div class="fp-datos">
      ${dato('N° de orden', p.ot || 'Por asignar')}
      ${dato('Pedido', borrador ? `${codigo} <small>(al registrar)</small>` : codigo)}
      ${dato('Fecha de registro', fechaCorta(p.fecha))}
      ${dato('Cliente', esc(c.nombre || '—'))}
      ${dato('DNI', esc(p.dni))}
      ${dato('Institución / club', esc(c.institucion || '—'))}
      ${dato('Teléfono', esc(c.telefono || '—'))}
      ${dato('Fecha de entrega', fechaCorta(p.fechaEntrega))}
      ${dato('Total de prendas', p.cantidad)}
    </div>
    <section class="fp-bloque">
      <h2>Dibujo técnico · vista frontal</h2>
      <div class="fp-dibujos">${dibujos}</div>
    </section>
    ${prendas}
    <div class="fp-dos">
      <section class="fp-bloque">
        <h2>Insumos a descontar</h2>
        <table class="fp-tabla">${insumos || '<tr><td>Sin insumos calculados</td><td></td></tr>'}</table>
      </section>
      <section class="fp-bloque">
        <h2>Detalles a cuidar</h2>
        <ul class="fp-lista">${cuidados.map((x) => `<li>${x}</li>`).join('')}</ul>
      </section>
    </div>
    <div class="fp-dos">
      <section class="fp-bloque">
        <h2>Control de calidad</h2>
        <table class="fp-tabla">
          <tr><td><strong>Criterio</strong></td><td><strong>Aprobado &nbsp; Rechazado</strong></td></tr>
          ${criterios.map((x) => `<tr><td>${x}</td><td><span class="fp-check"></span>Sí &nbsp;&nbsp;&nbsp;&nbsp; <span class="fp-check"></span>No</td></tr>`).join('')}
        </table>
      </section>
      <section class="fp-bloque">
        <h2>Autorizaciones</h2>
        <div class="fp-firmas">
          <div>Administrador(a)</div>
          <div>Confeccionista / taller</div>
        </div>
      </section>
    </div>
    <footer class="fp-pie">
      <span>Generado el ${generado} por ${esc(u.nombre)}</span>
      <span>${esc(empresa.nombre)} · ${esc(empresa.direccion)}</span>
    </footer>
  </div>`;
}

/* ---------- Ventana de la ficha (se abre encima del formulario del pedido) ---------- */
function abrirFichaProduccion(p) {
  if (!p) return;
  let modal = $('#modalFichaProd');
  if (!modal) {
    modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modalFichaProd';
    modal.tabIndex = -1;
    modal.style.zIndex = 1075;
    modal.setAttribute('aria-labelledby', 'fichaProdTitulo');
    modal.innerHTML = `
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <div class="d-flex align-items-center gap-3">
              <span class="icon-chip soft-amarillo"><i class="bi bi-file-earmark-pdf"></i></span>
              <div>
                <h5 class="modal-title mb-0" id="fichaProdTitulo">Ficha de producción</h5>
                <small class="text-muted" id="fichaProdSub"></small>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body"><div class="ficha-preview" id="fichaProdContenido"></div></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-lavanda" id="fichaProdImprimir"><i class="bi bi-printer me-1"></i> Imprimir</button>
            <button type="button" class="btn btn-primary" id="fichaProdPdf"><i class="bi bi-download me-1"></i> Descargar PDF</button>
          </div>
        </div>
      </div>`;
    document.body.appendChild(modal);
    // si se abre sobre el formulario del pedido, su fondo oscuro queda por encima del formulario
    modal.addEventListener('shown.bs.modal', () => {
      const fondos = $$('.modal-backdrop');
      if (fondos.length > 1) fondos[fondos.length - 1].style.zIndex = 1070;
    });
    modal.addEventListener('hidden.bs.modal', () => {
      if ($('.modal.show')) document.body.classList.add('modal-open');
    });
  }

  const html = htmlFicha(p);
  const nombre = `Ficha-produccion-${p.ot || p.codigo || 'nuevo-pedido'}`;
  $('#fichaProdContenido', modal).innerHTML = html;
  $('#fichaProdSub', modal).textContent = p.codigo
    ? `Pedido ${p.codigo}${p.ot ? ' · ' + p.ot : ''} · lista para imprimir o descargar`
    : 'Vista previa del pedido que se está registrando';
  $('#fichaProdImprimir', modal).onclick = () => imprimirContenido(nombre, '<style>@page { size: A4; margin: 8mm; }</style>' + html);
  $('#fichaProdPdf', modal).onclick = (e) => descargarFichaPdf(html, nombre, e.currentTarget);
  bootstrap.Modal.getOrCreateInstance(modal).show();
}

function cargarScript(url) {
  cargarScript.promesas = cargarScript.promesas || {};
  if (!cargarScript.promesas[url]) {
    cargarScript.promesas[url] = new Promise((resolver, rechazar) => {
      const s = document.createElement('script');
      s.src = url;
      s.onload = resolver;
      s.onerror = () => {
        delete cargarScript.promesas[url];
        rechazar(new Error('No se pudo cargar ' + url));
      };
      document.head.appendChild(s);
    });
  }
  return cargarScript.promesas[url];
}

async function descargarFichaPdf(html, nombre, boton) {
  const original = boton.innerHTML;
  boton.disabled = true;
  boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Generando…';
  const hoja = document.createElement('div');
  hoja.style.cssText = 'position:fixed;left:-10000px;top:0;width:780px;background:#fff';
  hoja.innerHTML = html;
  try {
    await cargarScript(HTML2PDF_URL);
    document.body.appendChild(hoja);
    await html2pdf().set({
      margin: [8, 8, 8, 8],
      filename: nombre + '.pdf',
      image: { type: 'jpeg', quality: 0.97 },
      html2canvas: { scale: 2, useCORS: true, backgroundColor: '#FFFFFF' },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
      pagebreak: { mode: ['css', 'legacy'], avoid: ['.ficha-prod > .fp-bloque', '.fp-dos', '.fp-datos', '.fp-top'] }
    }).from(hoja.firstElementChild).save();
    mostrarToast(`Ficha descargada: <strong>${esc(nombre)}.pdf</strong>`, 'exito');
  } catch (e) {
    mostrarToast('No se pudo crear el PDF directamente. Se abrirá la impresión: elige <strong>Guardar como PDF</strong>.', 'aviso');
    imprimirContenido(nombre, '<style>@page { size: A4; margin: 8mm; }</style>' + html);
  } finally {
    hoja.remove();
    boton.disabled = false;
    boton.innerHTML = original;
  }
}
