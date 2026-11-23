@extends('template.administrador')

@section('title', 'INVENTARIO')
@section('name_section', 'INVENTARIO')
@section('description', 'Actualizar inventario')
@section('item_section', 'Actualizar registro')
@section('route_section', route('inventories.index'))
@section('section', 'Inventario')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('inventories.update', $inventory) }}" method="POST">
      @csrf
      @method('PUT')
      @include('gestion.inventories.form')
    </form>
  </div>
</div>
@endsection
