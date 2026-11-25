@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Menú principal y resumen de pedidos, producción e inventario.')
@section('item_section', 'Inicio')
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
{{-- Resumen con datos reales de la base de datos --}}
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <a href="{{ route('orders.index') }}" class="card stat-card text-decoration-none">
      <div class="card-body">
        <span class="stat-icon soft-lavanda"><i class="bi bi-clipboard-check"></i></span>
        <div>
          <div class="stat-value">{{ $resumen['pedidos'] }}</div>
          <div class="stat-label">Pedidos activos</div>
          <div class="stat-foot text-muted">{{ $resumen['listos'] }} listos para entregar</div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <a href="{{ route('productions.index') }}" class="card stat-card text-decoration-none">
      <div class="card-body">
        <span class="stat-icon soft-azul"><i class="bi bi-scissors"></i></span>
        <div>
          <div class="stat-value">{{ $resumen['produccion'] }}</div>
          <div class="stat-label">Trabajos en producción</div>
          <div class="stat-foot text-muted">{{ $resumen['porIniciar'] }} por iniciar</div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <a href="{{ route('inventories.index', ['alerts' => 1]) }}" class="card stat-card text-decoration-none">
      <div class="card-body">
        <span class="stat-icon soft-amarillo"><i class="bi bi-box-seam"></i></span>
        <div>
          <div class="stat-value">{{ $resumen['stockBajo'] }}</div>
          <div class="stat-label">Productos con stock bajo</div>
          <div class="stat-foot {{ $resumen['stockBajo'] ? 'text-danger' : 'text-muted' }}">{{ $resumen['stockBajo'] ? 'Revisar alertas de stock' : 'Todo en orden' }}</div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <a href="{{ route('payments.index') }}" class="card stat-card text-decoration-none">
      <div class="card-body">
        <span class="stat-icon soft-rosa"><i class="bi bi-cash-coin"></i></span>
        <div>
          <div class="stat-value">S/ {{ number_format($resumen['porCobrar'], 2) }}</div>
          <div class="stat-label">Saldo por cobrar</div>
          <div class="stat-foot text-muted">De pedidos no entregados</div>
        </div>
      </div>
    </a>
  </div>
</div>

<div class="section-bar">
  <h3>Menú principal</h3>
</div>
<div class="tile-grid mb-4">
  <a class="module-tile" href="{{ route('customers.index') }}">
    <span class="tile-icon soft-lavanda"><i class="bi bi-people"></i></span>
    <span class="tile-title">Clientes</span>
    <span class="tile-sub">Registrar y buscar por DNI</span>
  </a>
  <a class="module-tile" href="{{ route('orders.index') }}">
    <span class="tile-icon soft-azul"><i class="bi bi-clipboard-check"></i></span>
    <span class="tile-title">Pedidos</span>
    <span class="tile-sub">Registrar pedidos personalizados</span>
  </a>
  <a class="module-tile" href="{{ route('sales.index') }}">
    <span class="tile-icon soft-rosa"><i class="bi bi-cart3"></i></span>
    <span class="tile-title">Ventas</span>
    <span class="tile-sub">Ventas con detalle e IGV</span>
  </a>
  <a class="module-tile" href="{{ route('orders.index') }}">
    <span class="tile-icon soft-salvia"><i class="bi bi-signpost-split"></i></span>
    <span class="tile-title">Seguimiento</span>
    <span class="tile-sub">Estado de cada pedido</span>
  </a>
  <a class="module-tile" href="{{ route('productions.index') }}">
    <span class="tile-icon soft-amarillo"><i class="bi bi-scissors"></i></span>
    <span class="tile-title">Producción</span>
    <span class="tile-sub">Órdenes de trabajo y etapas</span>
  </a>
  <a class="module-tile" href="{{ route('products.index') }}">
    <span class="tile-icon soft-lavanda"><i class="bi bi-boxes"></i></span>
    <span class="tile-title">Productos</span>
    <span class="tile-sub">Prendas, telas, hilos y accesorios</span>
  </a>
  <a class="module-tile" href="{{ route('inventories.index') }}">
    <span class="tile-icon soft-azul"><i class="bi bi-box-seam"></i></span>
    <span class="tile-title">Inventario</span>
    <span class="tile-sub">Stock actual y stock mínimo</span>
  </a>
  <a class="module-tile" href="{{ route('inventories.index', ['alerts' => 1]) }}">
    <span class="tile-icon soft-rosa"><i class="bi bi-exclamation-triangle"></i></span>
    <span class="tile-title">Alertas de stock</span>
    <span class="tile-sub">Insumos por reponer</span>
  </a>
  <a class="module-tile" href="{{ route('payments.index') }}">
    <span class="tile-icon soft-salvia"><i class="bi bi-cash-coin"></i></span>
    <span class="tile-title">Pagos</span>
    <span class="tile-sub">Adelantos, saldos y recibos</span>
  </a>
  <a class="module-tile" href="{{ route('deliveries.index') }}">
    <span class="tile-icon soft-amarillo"><i class="bi bi-truck"></i></span>
    <span class="tile-title">Entregas</span>
    <span class="tile-sub">Salida de pedidos terminados</span>
  </a>
  <a class="module-tile" href="{{ route('reports.index') }}">
    <span class="tile-icon soft-azul"><i class="bi bi-bar-chart-line"></i></span>
    <span class="tile-title">Reportes</span>
    <span class="tile-sub">Reportes e informes</span>
  </a>
</div>
@endsection
