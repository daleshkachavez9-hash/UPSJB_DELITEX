@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Menú principal y resumen de pedidos, producción e inventario.')
@section('item_section', 'Inicio')
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
<div class="section-bar">
  <h3>Menú principal</h3>
</div>
<div class="tile-grid mb-4">
  <a class="module-tile" href="{{ route('customers.index') }}">
    <span class="tile-icon soft-lavanda"><i class="bi bi-people"></i></span>
    <span class="tile-title">Clientes</span>
    <span class="tile-sub">Registrar y buscar por DNI</span>
  </a>
  <a class="module-tile" href="#">
    <span class="tile-icon soft-azul"><i class="bi bi-clipboard-check"></i></span>
    <span class="tile-title">Pedidos</span>
    <span class="tile-sub">Registrar pedidos personalizados</span>
  </a>
  <a class="module-tile" href="#">
    <span class="tile-icon soft-salvia"><i class="bi bi-signpost-split"></i></span>
    <span class="tile-title">Seguimiento</span>
    <span class="tile-sub">Estado de cada pedido</span>
  </a>
  <a class="module-tile" href="#">
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
  <a class="module-tile" href="#">
    <span class="tile-icon soft-salvia"><i class="bi bi-cash-coin"></i></span>
    <span class="tile-title">Pagos</span>
    <span class="tile-sub">Adelantos, saldos y recibos</span>
  </a>
  <a class="module-tile" href="#">
    <span class="tile-icon soft-amarillo"><i class="bi bi-truck"></i></span>
    <span class="tile-title">Entregas</span>
    <span class="tile-sub">Salida de pedidos terminados</span>
  </a>
  <a class="module-tile" href="#">
    <span class="tile-icon soft-azul"><i class="bi bi-bar-chart-line"></i></span>
    <span class="tile-title">Reportes</span>
    <span class="tile-sub">Reportes e informes</span>
  </a>
</div>
@endsection
