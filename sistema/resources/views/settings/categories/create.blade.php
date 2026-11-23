@extends('template.administrador')

@section('title', 'CATEGORÍAS')
@section('name_section', 'CATEGORÍAS')
@section('description', 'Nueva categoría')
@section('item_section', 'Nuevo registro')
@section('route_section', route('categories.index'))
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('categories.store') }}" method="POST">
      @csrf
      @include('settings.categories.form')
    </form>
  </div>
</div>
@endsection
