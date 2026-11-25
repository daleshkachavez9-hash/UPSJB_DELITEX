@extends('template.administrador')

@section('title', 'PEDIDOS')
@section('name_section', 'PEDIDOS')
@section('description', 'Registrar pedido personalizado')
@section('item_section', 'Nuevo pedido')
@section('route_section', route('orders.index'))
@section('section', 'Pedidos')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('gestion.orders.form')
    </form>
  </div>
</div>

@include('gestion.orders.extras')
@endsection
