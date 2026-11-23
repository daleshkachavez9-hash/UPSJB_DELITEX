@extends('template.administrador')

@section('title', 'ROLES')
@section('name_section', 'ROLES')
@section('description', 'Actualizar rol')
@section('item_section', 'Actualizar registro')
@section('route_section', route('roles.index'))
@section('section', 'Roles')
@section('module', 'Configuraciones')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('roles.update', $role) }}" method="POST">
      @csrf
      @method('PUT')
      @include('settings.roles.form')
    </form>
  </div>
</div>
@endsection
