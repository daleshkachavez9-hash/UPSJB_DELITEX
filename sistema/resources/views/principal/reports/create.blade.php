@extends('template.administrador')

@section('title', 'REPORTES')
@section('name_section', 'REPORTES')
@section('description', 'Generar reporte')
@section('item_section', 'Nuevo reporte')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('reports.store') }}" method="POST">
      @csrf
      @include('principal.reports.form')
    </form>
  </div>
</div>
@endsection
