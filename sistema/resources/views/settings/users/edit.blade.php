@extends('template.administrador')

@section('title', 'USUARIOS')
@section('name_section', 'USUARIOS')
@section('description', 'Actualizar usuario')
@section('item_section', 'Actualizar registro')
@section('route_section', route('users.index'))
@section('section', 'Usuarios')
@section('module', 'Configuraciones')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('users.update', $user) }}" method="POST">
      @csrf
      @method('PUT')
      @include('settings.users.form')
    </form>
  </div>
</div>
@endsection
