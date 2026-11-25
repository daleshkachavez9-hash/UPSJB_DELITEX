{{-- Ficha de producción del pedido: instrucciones para el taller.
     Es una página aparte (sin menú) para imprimirla o guardarla en PDF con el botón del navegador. --}}
@php
  $numero = str_pad($order->id, 6, '0', STR_PAD_LEFT);
  $colores = [
    'azul marino' => '#22385E', 'blanco' => '#FFFFFF', 'amarillo' => '#F2C94C', 'negro' => '#2A2D34',
    'rojo' => '#D2474B', 'verde' => '#3F9A62', 'azul' => '#3D6FC4', 'guinda' => '#7B2335',
    'celeste' => '#8EC9EE', 'gris' => '#9AA3AE', 'naranja' => '#F08A3C', 'morado' => '#7D55B5',
    'rosado' => '#F2A7C0', 'fucsia' => '#D6408C', 'plomo' => '#8A929C', 'dorado' => '#C9A43A',
  ];
  $conEstampado = $prendas->contains(fn ($p) => $p['detalle']->print_type !== 'Sin estampado');
  $responsables = $order->productions->pluck('user.name')->unique()->implode(', ');
  $cuidados = collect($prendas)->filter(fn ($p) => $p['detalle']->notes)
      ->map(fn ($p, $k) => 'Prenda ' . ($k + 1) . ': ' . $p['detalle']->notes)->values()->all();
  if ($conEstampado) {
      $cuidados[] = 'Estampado centrado y simétrico; revisar nombres y números con la lista del cliente';
  }
  $cuidados = array_merge($cuidados, [
      'Verificar que el color de la tela coincida con el pedido',
      'Costuras alineadas y sin hilos sueltos',
      'Separar y etiquetar las prendas por talla',
  ]);
  $criterios = array_merge(['Medidas', 'Costuras'], $conEstampado ? ['Estampado'] : [], ['Acabado', 'Cantidad por talla']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ficha de producción · Pedido Nº {{ $numero }} | DELITEX</title>
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  <style>
    body { background: #EEF1F6; }
    .ficha-barra { max-width: 760px; margin: 16px auto 10px; display: flex; gap: 8px; flex-wrap: wrap; }
    .ficha-prod { box-shadow: 0 4px 18px rgba(43, 58, 82, .12); border-radius: 12px; margin-bottom: 24px; }
    .fp-referencia { display: flex; gap: 14px; align-items: center; padding: 12px 14px; }
    .fp-referencia img { max-width: 220px; max-height: 160px; object-fit: contain; border: 1px solid #DDE3EC; border-radius: 8px; background: #fff; }
    @media print {
      body { background: #FFFFFF; }
      .ficha-barra { display: none; }
      .ficha-prod { box-shadow: none; margin: 0; }
    }
  </style>
</head>
<body>
  <div class="ficha-barra">
    <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimir o guardar en PDF</button>
    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i> Volver al pedido</a>
  </div>

  <div class="ficha-prod">
    <header class="fp-top">
      <span class="fp-logo"><img src="{{ asset('images/logo-emblema.svg') }}" alt="Logo DELITEX" width="56" height="56"></span>
      <div>
        <h1>FICHA DE PRODUCCIÓN</h1>
        <p>Instrucciones del pedido para el taller</p>
      </div>
      <div class="fp-empresa"><strong>DELITEX</strong>Confecciones deportivas</div>
    </header>

    <div class="fp-datos">
      <div class="fp-dato"><span>Pedido</span><strong>Nº {{ $numero }}</strong></div>
      <div class="fp-dato"><span>Fecha del pedido</span><strong>{{ $order->order_date->format('d/m/Y') }}</strong></div>
      <div class="fp-dato"><span>Estado</span><strong>{{ \App\Models\Order::ESTADOS[$order->status] ?? $order->status }}</strong></div>
      <div class="fp-dato"><span>Cliente</span><strong>{{ $order->customer->name }}</strong></div>
      <div class="fp-dato"><span>Documento</span><strong>{{ $order->customer->documento() }}</strong></div>
      <div class="fp-dato"><span>Teléfono</span><strong>{{ $order->customer->phone ?? '—' }}</strong></div>
      <div class="fp-dato"><span>Fecha de entrega</span><strong>{{ $order->delivery?->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</strong></div>
      <div class="fp-dato"><span>Responsable(s)</span><strong>{{ $responsables ?: 'Por asignar' }}</strong></div>
      <div class="fp-dato"><span>Total de prendas</span><strong>{{ $order->details->sum('quantity') }}</strong></div>
    </div>

    @if ($order->description || $order->reference_image)
      <section class="fp-bloque">
        <h2>Referencia del cliente</h2>
        <div class="fp-referencia">
          @if ($order->reference_image)
            <img src="{{ asset('storage/' . $order->reference_image) }}" alt="Imagen de referencia del pedido">
          @endif
          <div>{{ $order->description ?? 'Sin descripción.' }}</div>
        </div>
      </section>
    @endif

    <section class="fp-bloque">
      <h2>Dibujo técnico · vista frontal</h2>
      <div class="fp-dibujos">
        @foreach ($prendas as $k => $p)
          @php $d = $p['detalle']; @endphp
          <div class="fp-dibujo">
            @include('gestion.orders.dibujo-prenda', ['tipo' => $d->tipoPrenda(), 'color' => $d->color, 'estampado' => $d->print_type])
            <strong>{{ $d->nombrePrenda() }}</strong>
            <small>Prenda {{ $k + 1 }} · {{ $d->color ?: '—' }} · {{ $d->print_type === 'Sin estampado' ? 'sin estampado' : mb_strtolower($d->print_type) }}</small>
          </div>
        @endforeach
      </div>
    </section>

    @foreach ($prendas as $k => $p)
      @php
        $d = $p['detalle'];
        $q = $p['cantidad'];
        $sinEstampado = $d->print_type === 'Sin estampado';
        $reparto = $p['tallas']->map(fn ($n, $t) => "<strong>$n</strong> en talla <strong>" . e($t) . '</strong>')->implode(', ');
      @endphp
      <section class="fp-bloque">
        <h2>Prenda {{ $k + 1 }} · {{ $d->nombrePrenda() }} <small>{{ $q }} {{ $q === 1 ? 'unidad' : 'unidades' }}</small></h2>
        <div class="fp-prenda-cuerpo">
          <table class="fp-tabla">
            <tr><td>Tipo de prenda</td><td>{{ $d->tipoPrenda() }}</td></tr>
            <tr><td>Tipo de tela</td><td>{{ $d->fabric ?? 'Por definir' }}</td></tr>
            <tr><td>Color</td><td><span class="fp-color" style="background: {{ $colores[mb_strtolower(trim((string) $d->color))] ?? '#C9D3E0' }}"></span>{{ $d->color ?: '—' }}</td></tr>
            <tr><td>Estampado</td><td>{{ $d->print_type }}</td></tr>
            @unless ($sinEstampado)
              <tr><td>Detalle</td><td>{{ $d->print_detail ?: '—' }}</td></tr>
            @endunless
            <tr><td>Observaciones</td><td>{{ $d->notes ?: '—' }}</td></tr>
          </table>
          <div class="fp-tallas">
            @foreach ($p['tallas'] as $talla => $cantidad)
              <div><span>{{ $talla }}</span><b>{{ $cantidad }}</b></div>
            @endforeach
            <div class="total"><span>Total</span><b>{{ $q }}</b></div>
          </div>
        </div>
        <ol class="fp-pasos">
          <li><span class="n">1</span><div><span class="t">Corte</span>Cortar {{ $q }} {{ $q === 1 ? 'unidad' : 'unidades' }} de {{ mb_strtolower($d->tipoPrenda()) }}{{ $d->fabric ? ' en ' . mb_strtolower($d->fabric) : '' }}{{ $d->color ? ' ' . mb_strtolower($d->color) : '' }}: {!! $reparto !!}.</div></li>
          <li><span class="n">2</span><div><span class="t">Confección</span>Unir las piezas con hilo poliéster del color de la tela y revisar las medidas por talla.</div></li>
          <li class="{{ $sinEstampado ? 'omitido' : '' }}"><span class="n">3</span><div><span class="t">Estampado o bordado</span>{{ $sinEstampado ? 'Sin estampado: se omite este paso.' : $d->print_type . ($d->print_detail ? ' — ' . $d->print_detail : '') . '.' }}</div></li>
          <li><span class="n">4</span><div><span class="t">Acabado</span>Cortar hilos, planchar y embolsar separando por talla.</div></li>
          <li><span class="n">5</span><div><span class="t">Control de calidad</span>Revisar medidas, costuras{{ $sinEstampado ? '' : ', estampado' }} y la cantidad de cada talla antes de pasar a entrega.</div></li>
        </ol>
      </section>
    @endforeach

    <div class="fp-dos">
      <section class="fp-bloque">
        <h2>Detalles a cuidar</h2>
        <ul class="fp-lista">
          @foreach ($cuidados as $cuidado)
            <li>{{ $cuidado }}</li>
          @endforeach
        </ul>
      </section>
      <section class="fp-bloque">
        <h2>Control de calidad</h2>
        <table class="fp-tabla">
          <tr><td><strong>Criterio</strong></td><td><strong>Aprobado &nbsp; Rechazado</strong></td></tr>
          @foreach ($criterios as $criterio)
            <tr><td>{{ $criterio }}</td><td><span class="fp-check"></span>Sí &nbsp;&nbsp;&nbsp;&nbsp; <span class="fp-check"></span>No</td></tr>
          @endforeach
        </table>
      </section>
    </div>

    <section class="fp-bloque">
      <h2>Autorizaciones</h2>
      <div class="fp-firmas d-flex gap-4">
        <div class="flex-fill">Administrador(a)</div>
        <div class="flex-fill">Confeccionista / taller</div>
      </div>
    </section>

    <footer class="fp-pie">
      <span>Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</span>
      <span>DELITEX Confecciones · Pedido Nº {{ $numero }}</span>
    </footer>
  </div>
</body>
</html>
