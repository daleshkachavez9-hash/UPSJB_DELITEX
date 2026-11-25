{{-- Formulario compartido por create y edit.
     Las ventanas de búsqueda y el JavaScript están en extras.blade.php, fuera del <form>. --}}
@php
  $clienteElegido = $customers->firstWhere('id', old('customer_id', $order->customer_id ?? null));
  // Filas del detalle: lo enviado si falló la validación, o lo guardado al editar
  $filas = old('items', isset($order)
      ? $order->details->map(fn ($d) => [
          'product_id' => $d->product_id,
          'garment_type' => $d->garment_type,
          'size' => $d->size,
          'color' => $d->color,
          'fabric' => $d->fabric,
          'print_type' => $d->print_type,
          'print_detail' => $d->print_detail,
          'notes' => $d->notes,
          'quantity' => $d->quantity,
          'unit_price' => $d->unit_price,
        ])->all()
      : []);
@endphp

<div class="row g-3">
  <div class="col-md-6">
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
    <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $order->customer_id ?? '') }}">
  </div>

  <div class="col-md-3">
    <label for="order_date" class="form-label">Fecha del pedido</label>
    <input type="date" name="order_date" id="order_date" max="{{ now()->format('Y-m-d') }}"
           class="form-control @error('order_date') is-invalid @enderror"
           value="{{ old('order_date', isset($order) ? $order->order_date->format('Y-m-d') : now()->format('Y-m-d')) }}">
    @error('order_date')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-12">
    <label for="description" class="form-label">Descripción <small class="text-muted">(diseño, estampado, nombres y números, fecha de entrega acordada)</small></label>
    <textarea name="description" id="description" rows="2" maxlength="1000"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $order->description ?? '') }}</textarea>
    @error('description')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Imagen de referencia: logo, escudo o diseño que envía el cliente (aparece en la ficha de producción) --}}
  <div class="col-md-6">
    <label for="reference_image" class="form-label">Imagen de referencia <small class="text-muted">(opcional, JPG, PNG o WEBP de hasta 2 MB)</small></label>
    <input type="file" name="reference_image" id="reference_image" accept="image/jpeg,image/png,image/webp"
           class="form-control @error('reference_image') is-invalid @enderror">
    @error('reference_image')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
  @if (isset($order) && $order->reference_image)
    <div class="col-md-6 d-flex align-items-center gap-3">
      <img src="{{ asset('storage/' . $order->reference_image) }}" alt="Imagen de referencia del pedido"
           class="rounded border" style="max-height: 80px; max-width: 120px; object-fit: contain;">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
        <label class="form-check-label small" for="remove_image">Quitar la imagen actual</label>
      </div>
    </div>
  @endif
</div>

<h6 class="text-uppercase small text-muted mt-4 mb-2">Prendas del pedido</h6>

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
  <table class="table table-sm align-middle table-borderless" id="items">
    <thead>
      <tr>
        <th class="small text-uppercase" style="min-width: 190px">Prenda</th>
        <th class="small text-uppercase" style="width: 90px">Talla</th>
        <th class="small text-uppercase" style="width: 120px">Color</th>
        <th class="small text-uppercase text-center" style="width: 90px">Cantidad</th>
        <th class="small text-uppercase text-end" style="width: 120px">Precio unit.</th>
        <th class="small text-uppercase text-end" style="width: 110px">Subtotal</th>
        <th style="width: 50px"></th>
      </tr>
    </thead>
      @foreach ($filas as $i => $item)
        @include('gestion.orders.item-row', [
          'index' => $i,
          'item' => $item,
          // Si el producto ya no está activo, se toma del propio pedido para no perderlo al editar
          'product' => $products->firstWhere('id', $item['product_id'] ?? null)
              ?? (isset($order) ? $order->details->firstWhere('product_id', $item['product_id'] ?? 0)?->product : null),
        ])
      @endforeach
    <tfoot>
      <tr>
        <td colspan="5" class="text-end fw-semibold">Total del pedido</td>
        <td class="text-end fw-bold" id="total">S/ 0.00</td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</div>
<p class="text-muted small" id="sin-items">Todavía no hay prendas. Agregue un producto del catálogo o una prenda personalizada.</p>

<div class="d-flex flex-wrap gap-2 mb-4">
  <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalProducto">
    <i class="bi bi-search me-1"></i> Agregar del catálogo
  </button>
  <button type="button" class="btn btn-outline-secondary btn-sm" id="add-custom">
    <i class="bi bi-plus-lg me-1"></i> Prenda personalizada
  </button>
</div>

<div class="d-flex gap-2">
  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar pedido</button>
  <a href="{{ isset($order) ? route('orders.show', $order) : route('orders.index') }}" class="btn btn-outline-dark">Cancelar</a>
</div>
