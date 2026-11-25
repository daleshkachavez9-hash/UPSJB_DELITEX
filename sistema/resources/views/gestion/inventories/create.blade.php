@extends('template.administrador')

@section('title', 'INVENTARIO')
@section('name_section', 'INVENTARIO')
@section('description', 'Nuevo registro de inventario')
@section('item_section', 'Nuevo registro')
@section('route_section', route('inventories.index'))
@section('section', 'Inventario')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('inventories.store') }}" method="POST">
      @csrf
      @include('gestion.inventories.form')
    </form>
  </div>
</div>
@endsection
