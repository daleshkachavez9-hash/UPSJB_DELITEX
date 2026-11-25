@extends('template.administrador')

@section('title', 'VENTAS')
@section('name_section', 'VENTAS')
@section('description', 'Detalle de la venta')
@section('item_section', 'Detalle')
@section('route_section', route('sales.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@section('content')
@include('partials.alerts')

<div class="card">
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Venta Nº</small>
        {{-- str_pad: 7 se muestra como 000007 --}}
        <span class="fw-semibold">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Fecha</small>
        <span class="fw-semibold">{{ $sale->date->format('d/m/Y H:i') }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Vendedor</small>
        <span class="fw-semibold">{{ $sale->user->name }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Cliente</small>
        <span class="fw-semibold">{{ $sale->customer->name }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">DNI</small>
        <span class="fw-semibold">{{ $sale->customer->dni }}</span>
      </div>
      <div class="col-12 col-md-4 mb-2">
        <small class="text-muted d-block">Pedido</small>
        <span class="fw-semibold">{{ $sale->order ? 'Nº ' . str_pad($sale->order->id, 6, '0', STR_PAD_LEFT) : 'Venta directa' }}</span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th class="small text-uppercase text-center">Nº</th>
            <th class="small text-uppercase">Producto</th>
            <th class="small text-uppercase text-center">Cantidad</th>
            <th class="small text-uppercase text-end">Precio unit.</th>
            <th class="small text-uppercase text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sale->details as $detail)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $detail->product->name }}</td>
              <td class="text-center">{{ $detail->quantity }}</td>
              <td class="text-end">S/ {{ number_format($detail->unit_price, 2) }}</td>
              <td class="text-end">S/ {{ number_format($detail->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            {{-- El precio ya incluye IGV: op. gravada = total - igv --}}
            <td colspan="4" class="text-end">Op. gravada</td>
            <td class="text-end">S/ {{ number_format($sale->total - $sale->igv, 2) }}</td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">IGV (18%)</td>
            <td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td>
          </tr>
          <tr>
            <td colspan="4" class="text-end fw-semibold">Total</td>
            <td class="text-end fw-bold">S/ {{ number_format($sale->total, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <a href="{{ route('sales.index') }}" class="btn btn-outline-dark">Volver</a>
  </div>
</div>
@endsection
