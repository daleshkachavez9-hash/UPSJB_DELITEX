@extends('template.administrador')

@section('title', 'ROLES')
@section('name_section', 'ROLES')
@section('description', 'Nuevo rol')
@section('item_section', 'Nuevo registro')
@section('route_section', route('roles.index'))
@section('section', 'Roles')
@section('module', 'Configuraciones')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('roles.store') }}" method="POST">
      @csrf
      @include('settings.roles.form')
    </form>
  </div>
</div>
@endsection
