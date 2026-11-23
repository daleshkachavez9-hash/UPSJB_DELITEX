@extends('template.administrador')

@section('title', 'USUARIOS')
@section('name_section', 'USUARIOS')
@section('description', 'Nuevo usuario')
@section('item_section', 'Nuevo registro')
@section('route_section', route('users.index'))
@section('section', 'Usuarios')
@section('module', 'Configuraciones')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('users.store') }}" method="POST">
      @csrf
      @include('settings.users.form')
    </form>
  </div>
</div>
@endsection
