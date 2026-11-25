@extends('template.administrador')

@section('title', 'PEDIDOS')
@section('name_section', 'PEDIDOS')
@section('description', 'Modificar pedido Nº ' . str_pad($order->id, 6, '0', STR_PAD_LEFT))
@section('item_section', 'Modificar pedido')
@section('route_section', route('orders.index'))
@section('section', 'Pedidos')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('gestion.orders.form')
    </form>
  </div>
</div>

@include('gestion.orders.extras')
@endsection
