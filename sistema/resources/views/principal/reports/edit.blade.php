@extends('template.administrador')

@section('title', 'REPORTES')
@section('name_section', 'REPORTES')
@section('description', 'Editar reporte')
@section('item_section', 'Editar')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('reports.update', $report) }}" method="POST">
      @csrf
      @method('PUT')
      @include('principal.reports.form')
    </form>
    <small class="text-muted d-block mt-3">Si borra todo el contenido, el reporte se vuelve a generar con los datos actuales.</small>
  </div>
</div>
@endsection
