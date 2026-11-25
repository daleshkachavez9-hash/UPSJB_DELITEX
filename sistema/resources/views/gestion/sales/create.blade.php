@extends('template.administrador')

@section('title', 'VENTAS')
@section('name_section', 'VENTAS')
@section('description', 'Registrar nueva venta')
@section('item_section', 'Nueva venta')
@section('route_section', route('sales.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@php
  // Si la validación falla, se vuelve a mostrar lo que se había elegido
  $clienteElegido = $customers->firstWhere('id', old('customer_id'));
@endphp

{{-- Ventas: no hay edit; se registra, se consulta y, si hace falta, se anula --}}
@section('content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('sales.store') }}" method="POST">
      @csrf

      {{-- Datos generales --}}
      <div class="row g-3">
        <div class="col-md-5">
          <label for="customer_label" class="form-label">Cliente</label>
          {{-- Se busca por DNI o RUC, o por nombre; el id elegido viaja en el campo oculto customer_id --}}
          <div class="input-group has-validation">
            <input type="text" id="customer_label" readonly placeholder="Busque por DNI o RUC, o por nombre"
                   class="form-control bg-white @error('customer_id') is-invalid @enderror"
                   value="{{ $clienteElegido ? $clienteElegido->name . ' - ' . $clienteElegido->documento() : '' }}">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">
              <i class="bi bi-search me-1"></i> Buscar
            </button>
            @error('customer_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
        </div>

        <div class="col-md-7">
          {{-- El vendedor es el usuario que inició sesión: no se elige --}}
          <label class="form-label">Vendedor</label>
          <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>
      </div>
      <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Esta es una venta directa de productos del catálogo. El comprobante de un pedido personalizado se genera desde el pedido, cuando ya fue entregado.</p>

      {{-- Detalle de productos --}}
      <h6 class="text-uppercase small text-muted mt-4 mb-2">Productos</h6>

      {{-- Errores del detalle: falta de productos, repetidos o stock insuficiente --}}
      @if ($errors->has('items') || $errors->has('items.*'))
        <div class="alert alert-danger py-2">
          @foreach ($errors->get('items*') as $messages)
            @foreach ((array) $messages as $message)
              <div>{{ $message }}</div>
            @endforeach
          @endforeach
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th class="small text-uppercase">Producto</th>
              <th class="small text-uppercase text-center" style="width: 120px">Cantidad</th>
              <th class="small text-uppercase text-end" style="width: 120px">Precio</th>
              <th class="small text-uppercase text-end" style="width: 120px">Subtotal</th>
              <th style="width: 50px"></th>
            </tr>
          </thead>
          <tbody id="items">
            {{-- Si la validación falla, se vuelven a mostrar las filas enviadas --}}
            @foreach (old('items', []) as $i => $item)
              @include('gestion.sales.item-row', [
                'index' => $i,
                'item' => $item,
                'product' => $products->firstWhere('id', $item['product_id'] ?? null),
              ])
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end fw-semibold">Total (incluye IGV)</td>
              <td class="text-end fw-bold" id="total">S/ 0.00</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <p class="text-muted small" id="sin-items">Todavía no hay productos. Use el botón Agregar producto.</p>

      <button type="button" class="btn btn-outline-primary btn-sm mb-4" data-bs-toggle="modal" data-bs-target="#modalProducto">
        <i class="bi bi-plus-lg me-1"></i> Agregar producto
      </button>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Registrar venta</button>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-dark">Cancelar</a>
      </div>
    </form>
  </div>
</div>

{{-- Plantilla de una fila vacía (la copia el JavaScript) --}}
<template id="item-template">
  @include('gestion.sales.item-row', ['index' => '__INDEX__', 'item' => [], 'product' => null])
</template>

{{-- Ventanas de búsqueda --}}
@include('partials.modal-buscar', [
  'id' => 'modalCliente',
  'titulo' => 'Buscar cliente',
  'placeholder' => 'Escriba el DNI o RUC, o el nombre del cliente',
  'columnas' => ['Documento', 'Nombre', 'Teléfono'],
  'filas' => $customers->map(fn ($c) => [
    'id' => $c->id,
    'texto' => $c->document_number . ' ' . $c->name,
    'celdas' => [$c->documento(), $c->name, $c->phone ?? '—'],
    'datos' => ['label' => $c->name . ' - ' . $c->documento()],
  ])->all(),
])

@include('partials.modal-buscar', [
  'id' => 'modalProducto',
  'titulo' => 'Agregar producto',
  'placeholder' => 'Escriba el nombre o la categoría del producto',
  'columnas' => ['Producto', 'Categoría', 'Precio', 'Stock'],
  'filas' => $products->map(fn ($p) => [
    'id' => $p->id,
    'texto' => $p->name . ' ' . $p->category->name,
    'celdas' => [$p->name, $p->category->name, 'S/ ' . number_format($p->price, 2), rtrim(rtrim($p->quantity, '0'), '.') . ' ' . $p->unit],
    'datos' => ['name' => $p->name, 'price' => $p->price],
  ])->all(),
])
@endsection

@push('scripts')
<script src="{{ asset('js/buscador.js') }}"></script>
<script>
  const tbody = document.getElementById('items');
  const template = document.getElementById('item-template').innerHTML;
  let index = {{ count(old('items', [])) }};

  // Recalcula subtotales y total
  function recalculate() {
    let total = 0;
    tbody.querySelectorAll('tr').forEach(row => {
      const price = parseFloat(row.querySelector('.price').dataset.price || 0);
      const quantity = parseInt(row.querySelector('.quantity').value || 0);
      const subtotal = price * quantity;
      row.querySelector('.price').textContent = 'S/ ' + price.toFixed(2);
      row.querySelector('.subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
      total += subtotal;
    });
    document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
    document.getElementById('sin-items').classList.toggle('d-none', tbody.children.length > 0);
  }

  // Cliente elegido en la ventana de búsqueda
  document.getElementById('modalCliente').addEventListener('buscador:elegido', e => {
    document.getElementById('customer_id').value = e.detail.id;
    document.getElementById('customer_label').value = e.detail.label;
  });

  // Producto elegido: si ya está en la lista se aumenta su cantidad; si no, se agrega una fila
  document.getElementById('modalProducto').addEventListener('buscador:elegido', e => {
    const existente = tbody.querySelector(`tr[data-product-id="${e.detail.id}"]`);
    if (existente) {
      const cantidad = existente.querySelector('.quantity');
      cantidad.value = parseInt(cantidad.value || 0) + 1;
    } else {
      tbody.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index++));
      const fila = tbody.lastElementChild;
      fila.dataset.productId = e.detail.id;
      fila.querySelector('.product-id').value = e.detail.id;
      fila.querySelector('.product-name').textContent = e.detail.name;
      fila.querySelector('.price').dataset.price = e.detail.price;
    }
    recalculate();
  });

  // Quitar una fila
  tbody.addEventListener('click', e => {
    if (e.target.closest('.remove-item')) {
      e.target.closest('tr').remove();
      recalculate();
    }
  });

  // Recalcular al cambiar una cantidad
  tbody.addEventListener('input', recalculate);
  recalculate();
</script>
@endpush
