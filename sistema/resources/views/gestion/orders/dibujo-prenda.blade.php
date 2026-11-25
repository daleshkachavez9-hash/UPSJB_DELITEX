{{-- Dibujo técnico (vista frontal) de una prenda, pintado con el color de la tela y con la marca del estampado.
     Recibe: $tipo (tipo de prenda), $color (nombre del color) y $estampado. Es el mismo dibujo del prototipo. --}}
@php
  $colores = [
    'azul marino' => '#22385E', 'blanco' => '#FFFFFF', 'amarillo' => '#F2C94C', 'negro' => '#2A2D34',
    'rojo' => '#D2474B', 'verde' => '#3F9A62', 'azul' => '#3D6FC4', 'guinda' => '#7B2335',
    'celeste' => '#8EC9EE', 'gris' => '#9AA3AE', 'naranja' => '#F08A3C', 'morado' => '#7D55B5',
    'rosado' => '#F2A7C0', 'fucsia' => '#D6408C', 'plomo' => '#8A929C', 'dorado' => '#C9A43A',
  ];
  $relleno = $colores[mb_strtolower(trim($color ?? ''))] ?? '#C9D3E0';
  // Color oscuro: las líneas de detalle van en blanco
  [$r, $g, $b] = sscanf($relleno, '#%02x%02x%02x');
  $linea = (0.299 * $r + 0.587 * $g + 0.114 * $b) < 140 ? 'rgba(255,255,255,.75)' : 'rgba(43,58,82,.55)';
  $trazo = 'stroke="#2B3A52" stroke-width="2" stroke-linejoin="round"';
  $detalle = 'fill="none" stroke="' . $linea . '" stroke-width="1.4"';
  $clip = 'clip' . uniqid();

  $camiseta = 'M52 18 L68 12 Q80 24 92 12 L108 18 L134 40 L121 58 L108 50 L108 112 L52 112 L52 50 L39 58 L26 40 Z';
  $casaca = 'M52 18 L68 10 Q80 16 92 10 L108 18 L128 34 L142 102 L127 106 L110 54 L110 116 L50 116 L50 54 L33 106 L18 102 L32 34 Z';
  $short = fn ($y) => "M50 $y L110 $y L116 " . ($y + 48) . ' L86 ' . ($y + 48) . ' L80 ' . ($y + 20) . ' L74 ' . ($y + 48) . ' L44 ' . ($y + 48) . ' Z';
  $pantalon = fn ($y) => "M52 $y L108 $y L114 " . ($y + 76) . ' L90 ' . ($y + 76) . ' L80 ' . ($y + 22) . ' L70 ' . ($y + 76) . ' L46 ' . ($y + 76) . ' Z';

  // Marcas del estampado sobre la parte de arriba
  $marcas = function ($forma) use ($estampado, $clip, $linea) {
    $m = '';
    if ($estampado === 'Logo / escudo en el pecho') {
      $m .= '<path d="M88 30 h11 v6 q0 5 -5.5 7.5 q-5.5 -2.5 -5.5 -7.5 Z" fill="#F7E9BD" stroke="#2B3A52" stroke-width="1.2"/>';
    }
    if ($estampado === 'Sublimado completo') {
      $m .= '<clipPath id="' . $clip . '"><path d="' . $forma . '"/></clipPath>'
          . '<g clip-path="url(#' . $clip . ')" stroke="#FFFFFF" stroke-opacity=".45" stroke-width="7">'
          . '<path d="M10 70 L90 -10 M10 110 L130 -10 M30 130 L150 10 M70 130 L150 50"/></g>';
    }
    if ($estampado === 'Nombre y número en la espalda') {
      $m .= '<text x="80" y="84" text-anchor="middle" font-size="9" font-weight="800" fill="' . $linea . '">N° atrás</text>';
    }
    return $m;
  };
  $parteSuperior = fn ($forma, $extra) => '<path d="' . $forma . '" fill="' . $relleno . '" ' . $trazo . '/>' . $extra . $marcas($forma);

  $cuelloV = '<path d="M68 12 Q80 24 92 12" ' . $detalle . '/>';
  $cuelloPolo = '<path d="M68 12 L72 22 L80 28 L88 22 L92 12" ' . $detalle . '/><path d="M80 28 L80 44" ' . $detalle . '/>'
      . '<circle cx="80" cy="34" r="1.6" fill="' . $linea . '"/><circle cx="80" cy="40" r="1.6" fill="' . $linea . '"/>';
  $detallesCasaca = '<path d="M68 10 L68 4 L92 4 L92 10" fill="' . $relleno . '" ' . $trazo . '/>'
      . '<path d="M80 6 L80 116" ' . $detalle . ' stroke-dasharray="3 2.5"/><path d="M50 108 L110 108" ' . $detalle . '/>'
      . '<path d="M20 96 L34 100 M140 96 L126 100" ' . $detalle . '/>';
  $detallesShort = fn ($y) => '<path d="M50 ' . ($y + 6) . ' L110 ' . ($y + 6) . '" ' . $detalle . '/>';
  $detallesPantalon = fn ($y) => '<path d="M52 ' . ($y + 6) . ' L108 ' . ($y + 6) . '" ' . $detalle . '/>'
      . '<path d="M48 ' . ($y + 68) . ' L68 ' . ($y + 68) . ' M92 ' . ($y + 68) . ' L112 ' . ($y + 68) . '" ' . $detalle . '/>';

  // El tipo puede venir del catálogo ("Polo", "Short"...) o de la lista de prendas personalizadas
  $t = mb_strtolower($tipo ?? '');
  $cuerpo = match (true) {
    str_contains($t, 'conjunto') => '<g transform="translate(0 4)">' . $parteSuperior($camiseta, $cuelloV) . '</g>'
        . '<path d="' . $short(122) . '" fill="' . $relleno . '" ' . $trazo . '/>' . $detallesShort(122),
    str_contains($t, 'short') => '<path d="' . $short(64) . '" fill="' . $relleno . '" ' . $trazo . '/>' . $detallesShort(64),
    str_contains($t, 'buzo') => '<g transform="translate(16 2) scale(.8)">' . $parteSuperior($casaca, $detallesCasaca) . '</g>'
        . '<path d="' . $pantalon(102) . '" fill="' . $relleno . '" ' . $trazo . '/>' . $detallesPantalon(102),
    str_contains($t, 'casaca') => '<g transform="translate(0 38)">' . $parteSuperior($casaca, $detallesCasaca) . '</g>',
    str_contains($t, 'polo') => '<g transform="translate(0 40)">' . $parteSuperior($camiseta, $cuelloPolo) . '</g>',
    default => '<g transform="translate(0 40)">' . $parteSuperior($camiseta, $cuelloV) . '</g>',
  };
@endphp
<svg viewBox="0 0 160 200" width="136" height="170" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <ellipse cx="80" cy="190" rx="52" ry="5" fill="#2B3A52" opacity=".08"/>{!! $cuerpo !!}
</svg>
