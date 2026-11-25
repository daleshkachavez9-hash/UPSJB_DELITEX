@extends('template.administrador')

@section('title', 'PRODUCTOS')
@section('name_section', 'PRODUCTOS')
@section('description', 'Nuevo producto')
@section('item_section', 'Nuevo registro')
@section('route_section', route('products.index'))
@section('section', 'Productos')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('gestion.products.form')
    </form>
  </div>
</div>
@endsection
