@extends('template.administrador')

@section('title', 'REPORTES')
@section('name_section', 'REPORTES')
@section('description', 'Detalle del reporte')
@section('item_section', 'Detalle')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Principal')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Tipo</small>
        <span class="fw-semibold">{{ $report->type }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Fecha de generación</small>
        <span class="fw-semibold">{{ $report->generated_date->format('d/m/Y') }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Generado por</small>
        <span class="fw-semibold">{{ $report->user->name }}</span>
      </div>
    </div>

    {{-- white-space: pre-line respeta los saltos de línea del contenido --}}
    <div class="border rounded p-3 bg-light" style="white-space: pre-line">{{ $report->content }}</div>

    <div class="d-flex gap-2 mt-4">
      <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-dark"><i class="bi bi-pencil-square me-1"></i> Editar</a>
      <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
  </div>
</div>
@endsection
