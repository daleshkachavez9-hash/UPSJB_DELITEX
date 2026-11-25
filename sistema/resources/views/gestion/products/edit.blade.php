@extends('template.administrador')

@section('title', 'PRODUCTOS')
@section('name_section', 'PRODUCTOS')
@section('description', 'Actualizar producto')
@section('item_section', 'Actualizar registro')
@section('route_section', route('products.index'))
@section('section', 'Productos')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('gestion.products.form')
    </form>
  </div>
</div>
@endsection
