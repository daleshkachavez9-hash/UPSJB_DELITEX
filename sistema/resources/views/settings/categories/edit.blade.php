@extends('template.administrador')

@section('title', 'CATEGORÍAS')
@section('name_section', 'CATEGORÍAS')
@section('description', 'Actualizar categoría')
@section('item_section', 'Actualizar registro')
@section('route_section', route('categories.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('categories.update', $category) }}" method="POST">
      @csrf
      @method('PUT')
      @include('settings.categories.form')
    </form>
  </div>
</div>
@endsection
