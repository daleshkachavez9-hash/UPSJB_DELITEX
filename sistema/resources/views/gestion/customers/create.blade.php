@extends('template.administrador')

@section('title', 'CLIENTES')
@section('name_section', 'CLIENTES')
@section('description', 'Nuevo cliente')
@section('item_section', 'Nuevo registro')
@section('route_section', route('customers.index'))
@section('section', 'Clientes')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    {{-- POST /customers → CustomerController@store --}}
    <form action="{{ route('customers.store') }}" method="POST">
      @csrf
      @include('gestion.customers.form')
    </form>
  </div>
</div>
@endsection
