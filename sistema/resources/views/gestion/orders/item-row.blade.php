{{-- Una prenda del pedido: un producto del catálogo o una prenda personalizada (sin producto).
     items[0][...], items[1][...] llegan al controlador como el arreglo $request->items.
     Cada prenda ocupa dos filas: la primera con talla, color, cantidad y precio; la segunda con
     los datos que el taller necesita para la ficha de producción (tela, estampado y observaciones). --}}
@php
  $estampado = $item['print_type'] ?? 'Sin estampado';
@endphp
<tbody class="item">
  <tr>
    <td>
      <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id" value="{{ $product?->id }}">
      <span class="product-name fw-semibold {{ $product ? '' : 'd-none' }}">{{ $product?->name }}</span>
      {{-- Prenda personalizada: se elige su tipo (sirve para el dibujo de la ficha) --}}
      <select name="items[{{ $index }}][garment_type]" class="form-select form-select-sm garment-type {{ $product ? 'd-none' : '' }}"
              @disabled($product)>
        <option value="">Tipo de prenda…</option>
        @foreach (\App\Models\OrderDetail::PRENDAS as $prenda)
          <option @selected(($item['garment_type'] ?? '') === $prenda)>{{ $prenda }}</option>
        @endforeach
      </select>
    </td>
    <td>
      <input type="text" name="items[{{ $index }}][size]" maxlength="20" placeholder="Ej. M"
             class="form-control form-control-sm" value="{{ $item['size'] ?? '' }}">
    </td>
    <td>
      <input type="text" name="items[{{ $index }}][color]" maxlength="30" placeholder="Ej. Azul"
             class="form-control form-control-sm" value="{{ $item['color'] ?? '' }}">
    </td>
    <td>
      <input type="number" name="items[{{ $index }}][quantity]" min="1"
             class="form-control form-control-sm text-center quantity" value="{{ $item['quantity'] ?? 1 }}">
    </td>
    <td>
      <input type="number" name="items[{{ $index }}][unit_price]" min="0" step="0.01"
             class="form-control form-control-sm text-end unit-price" value="{{ $item['unit_price'] ?? '' }}">
    </td>
    <td class="text-end subtotal">S/ 0.00</td>
    <td class="text-center" rowspan="2">
      <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Quitar">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  </tr>
  <tr class="border-bottom">
    <td colspan="6" class="pt-0 pb-3">
      <div class="row g-2">
        <div class="col-6 col-md-2">
          <select name="items[{{ $index }}][fabric]" class="form-select form-select-sm" title="Tela">
            <option value="">Tela…</option>
            @foreach (\App\Models\OrderDetail::TELAS as $tela)
              <option @selected(($item['fabric'] ?? '') === $tela)>{{ $tela }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-3">
          <select name="items[{{ $index }}][print_type]" class="form-select form-select-sm print-type" title="Estampado">
            @foreach (\App\Models\OrderDetail::ESTAMPADOS as $opcion)
              <option @selected($estampado === $opcion)>{{ $opcion }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-3">
          <input type="text" name="items[{{ $index }}][print_detail]" maxlength="150" placeholder="Detalle del estampado"
                 class="form-control form-control-sm print-detail" value="{{ $item['print_detail'] ?? '' }}"
                 @disabled($estampado === 'Sin estampado')>
        </div>
        <div class="col-12 col-md-4">
          <input type="text" name="items[{{ $index }}][notes]" maxlength="200" placeholder="Observaciones (opcional)"
                 class="form-control form-control-sm" value="{{ $item['notes'] ?? '' }}">
        </div>
      </div>
    </td>
  </tr>
</tbody>
