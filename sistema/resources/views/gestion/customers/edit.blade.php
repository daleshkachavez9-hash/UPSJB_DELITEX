@extends('template.administrador')

@section('title', 'CLIENTES')
@section('name_section', 'CLIENTES')
@section('description', 'Actualizar cliente')
@section('item_section', 'Actualizar registro')
@section('section', 'Clientes')
@section('module', 'Gestión')

@section('content')
<div class="card">
  <div class="card-body">
    {{-- PUT /customers/{id} → CustomerController@update --}}
    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
      @csrf
      @method('PUT')
      @include('gestion.customers.form')
    </form>
  </div>
</div>
@endsection
